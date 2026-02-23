<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum\Calculator;

use Klass8s\Bundle\Checksum\ChecksumAnnotation;
use Klass8s\Bundle\Checksum\PodTemplateGetter\PodTemplateGetter;
use Klass8s\Bundle\Registry\APIResourceRegistry;
use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\Job;
use K8Schema\Api\Core\V1\ConfigMap;
use K8Schema\Api\Core\V1\Container;
use K8Schema\Api\Core\V1\Secret;
use Klass8s\Core\Container\Env\ExternalConfigurationRegistry;
use Klass8s\Renderer\JsonRenderer;

readonly class EnvSourcesCalculator implements ChecksumCalculatorInterface
{
    use ChecksumTrait;

    private const ANNOTATION_NAME = 'env-sources-checksum';

    public function __construct(
        private PodTemplateGetter $podTemplateGetter,
        private APIResourceRegistry $registry,
        private JsonRenderer $renderer,
        private ExternalConfigurationRegistry $externalConfigurations,
    ) {
    }

    public function calculate(Job|CronJob|Deployment|StatefulSet $workload): ChecksumAnnotation
    {
        $containers = $this->podTemplateGetter->get($workload)->spec()->containers();

        $envSources = [];
        foreach ($containers->all() as $container) {
            $envSources += $this->envVarSources($container);
            $envSources += $this->envFromSources($container);
        }

        return new ChecksumAnnotation(self::ANNOTATION_NAME, $this->checksum($envSources));
    }

    private function envFromSources(Container $container): array
    {
        $sources = [];
        foreach ($container->envFrom()->all() as $envSource) {
            if ($name = $envSource->getConfigMapRef()?->getName()) {
                $kind = ConfigMap::KIND;
            } elseif ($name = $envSource->getSecretRef()?->getName()) {
                $kind = Secret::KIND;
            } else {
                throw new \LogicException('EnvFromSource instance must contain either "configMapRef" or "secretRef" field');
            }

            $isExternal = match ($kind) {
                ConfigMap::KIND => $this->externalConfigurations->hasConfigMap($name),
                Secret::KIND => $this->externalConfigurations->hasSecret($name),
                default => throw new \LogicException(sprintf('Env source must be either ConfigMap or Secret, but got env source with kind "%s".', $kind)),
            };

            if ($isExternal) {
                continue;
            }

            $source = $this->getSource($name, $kind);
            $sources[spl_object_hash($source)] = $source;
        }

        return $sources;
    }

    private function envVarSources(Container $container): array
    {
        $sources = [];
        foreach ($container->env()->all() as $envVar) {
            $envVarSource = $envVar->getValueFrom();
            if ($configMapRef = $envVarSource?->getConfigMapKeyRef()) {
                $name = $configMapRef->getName();
                $kind = ConfigMap::KIND;
            } elseif ($secretRef = $envVarSource?->getSecretKeyRef()) {
                $name = $secretRef->getName();
                $kind = Secret::KIND;
            } else {
                continue;
            }

            $source = $this->getSource($name, $kind);
            $sources[spl_object_hash($source)] = $source;
        }

        return $sources;
    }

    private function getSource(string $name, string $kind): ConfigMap|Secret
    {
        if (!$this->registry->has($name, $kind)) {
            throw new \LogicException(sprintf('One of manifests uses "%s" as an env source, but no %s with such name was found', $name, $kind));
        }

        $apiResource =  $this->registry->get($name, $kind);
        assert($apiResource instanceof ConfigMap || $apiResource instanceof Secret, new \TypeError('$apiResource must be ConfigMap or Secret instance'));

        return $apiResource;
    }
}
