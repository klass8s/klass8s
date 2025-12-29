<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Autoscaling\V2\CrossVersionObjectReference;
use K8Schema\Api\Autoscaling\V2\HorizontalPodAutoscaler;
use K8Schema\Api\Autoscaling\V2\HorizontalPodAutoscalerSpec;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\Autoscaling\Configurator\BehaviorConfigurator;
use Klass8s\Core\Autoscaling\Configurator\MetricsConfigurator;
use Klass8s\Core\Autoscaling\HorizontalPodAutoscalerInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\VersionedManifestReference;
use Klass8s\Util\VersionedManifestReferencesService;

class HorizontalPodAutoscalerMaker extends AbstractResourceMaker
{
    public function __construct(private readonly VersionedManifestReferencesService $referencesService)
    {
    }

    protected function supportsClass(): string
    {
        return HorizontalPodAutoscalerInterface::class;
    }

    protected function makeResource(ManifestInterface|HorizontalPodAutoscalerInterface $manifest, AppInterface $app): APIResourceInterface
    {
        $scaleTargetRef = $this->scaleTargetRef($manifest->scaleTargetRef());

        $hpaSpec = new HorizontalPodAutoscalerSpec($manifest->maxReplicas(), $scaleTargetRef);
        $hpaSpec->setMinReplicas($manifest->minReplicas());

        $metricsConfigurator = new MetricsConfigurator($hpaSpec->metrics(), $this->referencesService);
        $manifest->metrics($metricsConfigurator);

        $behaviorConfigurator = new BehaviorConfigurator($hpaSpec->behavior());
        $manifest->behavior($behaviorConfigurator);

        $hpa = new HorizontalPodAutoscaler($hpaSpec);

        $app->metadataHelper()->configureMeta($manifest, $hpa);

        $manifest->configureHorizontalPodAutoscaler($hpa);

        return $hpa;
    }

    private function scaleTargetRef(VersionedManifestReference|CrossVersionObjectReference $reference): CrossVersionObjectReference
    {
        if ($reference instanceof CrossVersionObjectReference) {
            return $reference;
        }

        return $this->referencesService->toCrossVersionObjectReference($reference);
    }
}
