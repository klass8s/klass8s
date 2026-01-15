<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\PriorityClass;

use K8Schema\Api\Core\V1\PodSpec;
use Klass8s\App\AppInterface;
use Klass8s\Core\PriorityClass\PriorityClassInterface;
use Klass8s\Registry\AppRegistry;

class PriorityClassConfigurator
{
    private bool $locked = false;

    public function __construct(
        private readonly PodSpec $spec,
        private readonly AppInterface $app,
        private readonly AppRegistry $appRegistry,
    ) {
    }

    /**
     * @param class-string<PriorityClassInterface> $phpClassName
     */
    public function fromPHPClass(string $phpClassName): void
    {
        $this->prohibitMultipleCalls();
        if (!class_exists($phpClassName)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist', $phpClassName));
        }
        $class = new \ReflectionClass($phpClassName);
        if (!$class->implementsInterface(PriorityClassInterface::class)) {
            throw new \InvalidArgumentException(sprintf('$phpClassName must be a name of class that implements "%s", given "%s"', PriorityClassInterface::class, $phpClassName));
        }

        if (!$this->app->ownsManifestClass($phpClassName)) {
            $msg = <<<'ERR'
                App "%s" does not have manifests with class "%s". Please use method "%s::withExternalApp()"
                for using priority classes from another app. Example:

                $priorityClass->withExternalApp('external-app')->fromPHPClass(ExternalPriorityClass::class);
                ERR;

            throw new \InvalidArgumentException(sprintf($msg, $this->app->alias(), $phpClassName, static::class));
        }

        $priorityClassName = $this->app->resourceNameByManifestClass($phpClassName, PriorityClassInterface::class);
        $this->spec->setPriorityClassName($priorityClassName);
    }

    public function fromPriorityClassName(string $priorityClassName): void
    {
        $this->prohibitMultipleCalls();
        $this->spec->setPriorityClassName($priorityClassName);
    }

    public function name(): ?string
    {
        return $this->spec->getPriorityClassName();
    }

    public function withExternalApp(string $appAlias): static
    {
        return new self($this->spec, $this->appRegistry->get($appAlias), $this->appRegistry);
    }

    private function prohibitMultipleCalls(): void
    {
        if ($this->locked) {
            $err = <<<'ERR'
                %s allows to set priority class name only once, but multiple calls detected.
                Ensure you call only one of methods "fromPHPClass()" or "fromPriorityClassName()", and only once.
                ERR;

            throw new \LogicException(sprintf($err, static::class));
        }

        $this->locked = true;
    }
}
