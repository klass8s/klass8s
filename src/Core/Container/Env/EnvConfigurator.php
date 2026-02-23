<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Env;

use K8Schema\Api\Core\V1\ConfigMapKeySelector;
use K8Schema\Api\Core\V1\EnvFromSource;
use K8Schema\Api\Core\V1\EnvFromSourceList;
use K8Schema\Api\Core\V1\EnvVar;
use K8Schema\Api\Core\V1\EnvVarList;
use K8Schema\Api\Core\V1\SecretEnvSource;
use K8Schema\Api\Core\V1\SecretKeySelector;
use Klass8s\App\AppInterface;
use Klass8s\Core\ConfigMap\ConfigMapInterface;
use Klass8s\Core\Container\Resources\ContainerResourcesField;
use Klass8s\Core\Pod\PodField;
use Klass8s\Core\Secret\SecretInterface;
use Klass8s\Event\ConfigMapEnvSourceAddedEvent;
use Klass8s\Event\ConfigMapEnvSourceEvent;
use Klass8s\Event\SecretEnvSourceAddedEvent;
use Klass8s\Event\SecretEnvSourceEvent;
use Klass8s\Registry\AppRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class EnvConfigurator
{
    public function __construct(
        private EnvVarList $vars,
        private EnvFromSourceList $sources,
        private AppInterface $app,
        private AppRegistry $appRegistry,
        private EventDispatcherInterface $dispatcher,
        private ExternalConfigurationRegistry $externalConfigurations,
    ) {
    }

    public function addFromClasses(string ...$classes): static
    {
        foreach ($classes as $class) {
            $this->addFromClass($class);
        }

        return $this;
    }

    /**
     * @param class-string<SecretInterface>|class-string<ConfigMapInterface> $className
     * @param ?string $varNamesPrefix Allowed only for configmaps
     */
    public function addFromClass(string $className, bool $mustExist = true, ?string $varNamesPrefix = null): static
    {
        $class = new \ReflectionClass($className);

        if ($class->implementsInterface(ConfigMapInterface::class)) {
            return $this->addConfigMapClass($className, $mustExist, $varNamesPrefix);
        } elseif ($class->implementsInterface(SecretInterface::class)) {
            if (null !== $varNamesPrefix) {
                throw new \LogicException(sprintf('$varNamesPrefix must be specified only with config map classes, but specified with secret class "%s"', $className));
            }

            return $this->addSecretClass($className, $mustExist);
        }

        throw new \InvalidArgumentException(sprintf('$className must be a name of class, that implements either "%s" or "%s"', ConfigMapInterface::class, SecretInterface::class));
    }

    /**
     * @param class-string<ConfigMapInterface> $configMapClass
     */
    public function addConfigMapClass(string $configMapClass, bool $mustExist = true, ?string $varNamesPrefix = null): static
    {
        $event = new ConfigMapEnvSourceEvent($configMapClass, $this->app, $this);
        $this->dispatcher->dispatch($event, $event::NAME);
        if ($event->isPropagationStopped()) {
            return $this;
        }

        $this->ensureAppOwnsManifestClass($configMapClass);
        $cmName = $this->app->resourceNameFromManifestClass($configMapClass, ConfigMapInterface::class);

        $this->addConfigMap($cmName, $mustExist, $varNamesPrefix);

        $event = new ConfigMapEnvSourceAddedEvent($configMapClass, $this->app, $this);
        $this->dispatcher->dispatch($event, $event::NAME);

        return $this;
    }

    public function addConfigMap(string $configMapName, bool $mustExist = true, ?string $varNamesPrefix = null, bool $external = false): static
    {
        $envFromSource = new EnvFromSource();
        if (null !== $varNamesPrefix) {
            $envFromSource->setPrefix($varNamesPrefix);
        }
        $envFromSource->configMapRef()
            ->setName($configMapName)
            ->setOptional(!$mustExist);

        $this->sources->add($envFromSource);

        if ($external) {
            $this->externalConfigurations->addConfigMap($configMapName);
        }

        return $this;
    }

    /**
     * @param class-string<SecretInterface> $secretClass
     */
    public function addSecretClass(string $secretClass, bool $mustExist = true): static
    {
        $event = new SecretEnvSourceEvent($secretClass, $this->app, $this);
        $this->dispatcher->dispatch($event, $event::NAME);
        if ($event->isPropagationStopped()) {
            return $this;
        }

        $this->ensureAppOwnsManifestClass($secretClass);
        $secretName = $this->app->resourceNameFromManifestClass($secretClass, SecretInterface::class);

        $this->addSecret($secretName, $mustExist);

        $event = new SecretEnvSourceAddedEvent($secretClass, $this->app, $this);
        $this->dispatcher->dispatch($event, $event::NAME);

        return $this;
    }

    public function addSecret(string $secretName, bool $mustExist = true, bool $external = false): static
    {
        $envFromSource = new EnvFromSource();
        $envFromSource->secretRef()
            ->setName($secretName)
            ->setOptional(!$mustExist);

        $this->sources->add($envFromSource);

        if ($external) {
            $this->externalConfigurations->addSecret($secretName);
        }

        return $this;
    }

    public function var(string $name, string $value): static
    {
        $var = new EnvVar($name);
        $var->setValue($value);

        $this->vars->add($var);

        return $this;
    }

    /**
     * @param class-string<ConfigMapInterface> $configMapClass
     */
    public function varFromConfigMapClass(string $varName, string $configMapClass, string $configMapKey, bool $optional = false): static
    {
        $this->ensureAppOwnsManifestClass($configMapClass);
        $cmName = $this->app->resourceNameFromManifestClass($configMapClass, ConfigMapInterface::class);

        $keySelector = new ConfigMapKeySelector($configMapKey);
        $keySelector
            ->setName($cmName)
            ->setOptional($optional);

        $var = new EnvVar($varName);
        $var->valueFrom()->setConfigMapKeyRef($keySelector);
        $this->vars->add($var);

        return $this;
    }

    /**
     * @param class-string<SecretInterface> $secretClass
     */
    public function varFromSecretClass(string $varName, string $secretClass, string $secretKey, bool $optional = false): static
    {
        $this->ensureAppOwnsManifestClass($secretClass);
        $secretName = $this->app->resourceNameFromManifestClass($secretClass, SecretInterface::class);

        $keySelector = new SecretKeySelector($secretKey);
        $keySelector
            ->setName($secretName)
            ->setOptional($optional);

        $var = new EnvVar($varName);
        $var->valueFrom()->setSecretKeyRef($keySelector);
        $this->vars->add($var);

        return $this;
    }

    public function varFromPod(string $varName, PodField $podField): static
    {
        $fieldSelector = $podField->selector();

        $var = new EnvVar($varName);
        $var->valueFrom()->setFieldRef($fieldSelector);
        $this->vars->add($var);

        return $this;
    }

    public function varFromContainerResources(string $varName, ContainerResourcesField $field): static
    {
        $fieldSelector = $field->selector();

        $var = new EnvVar($varName);
        $var->valueFrom()->setResourceFieldRef($fieldSelector);
        $this->vars->add($var);

        return $this;
    }

    public function withExternalApp(string $appAlias): EnvConfigurator
    {
        return new EnvConfigurator(
            $this->vars,
            $this->sources,
            $this->appRegistry->get($appAlias),
            $this->appRegistry,
            $this->dispatcher,
            $this->externalConfigurations,
        );
    }

    private function ensureAppOwnsManifestClass(string $className): void
    {
        if ($this->app->ownsManifestClass($className)) {
            return;
        }

        $msg = <<<'ERR'
            App "%s" does not have manifests with class "%s". Please use method "%s::withExternalApp()"
            for adding configmaps or secrets from another app. Example:

            $env->withExternalApp('externalAppAlias')
                ->addConfigMap(MyConfigMap::class)
                ->addSecret(MySecret::class)
            ;
            ERR;

        throw new \InvalidArgumentException(
            sprintf($msg, $this->app->alias(), $className, EnvConfigurator::class)
        );
    }
}
