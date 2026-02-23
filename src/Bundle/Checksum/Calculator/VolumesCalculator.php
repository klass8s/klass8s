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
use K8Schema\Api\Core\V1\Secret;
use Klass8s\Renderer\JsonRenderer;

readonly class VolumesCalculator implements ChecksumCalculatorInterface
{
    use ChecksumTrait;

    private const ANNOTATION_NAME = 'volume-sources-checksum';

    public function __construct(
        private PodTemplateGetter $podTemplateGetter,
        private APIResourceRegistry $registry,
        private JsonRenderer $renderer,
    ) {
    }

    public function calculate(Job|CronJob|Deployment|StatefulSet $workload): ChecksumAnnotation
    {
        $volumes = $this->podTemplateGetter->get($workload)->spec()->volumes();

        $volumeSources = [];
        foreach ($volumes->all() as $volume) {
            if ($name = $volume->getConfigMap()?->getName()) {
                $kind = ConfigMap::KIND;
            } elseif ($name = $volume->getSecret()?->getSecretName()) {
                $kind = Secret::KIND;
            } else {
                continue;
            }

            $source = $this->getSource($name, $kind);
            $volumeSources[spl_object_hash($source)] = $source;
        }

        return new ChecksumAnnotation(self::ANNOTATION_NAME, $this->checksum($volumeSources));
    }

    private function getSource(string $name, string $kind): ConfigMap|Secret
    {
        if (!$this->registry->has($name, $kind)) {
            throw new \LogicException(sprintf('One of manifests uses "%s" as a volume source, but no %s with such name was found', $name, $kind));
        }

        return $this->registry->get($name, $kind);
    }
}
