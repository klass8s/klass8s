<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling;

use K8Schema\Api\Autoscaling\V2\HorizontalPodAutoscaler;
use Klass8s\Core\Autoscaling\Configurator\BehaviorConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractHorizontalPodAutoscaler extends AbstractManifest implements HorizontalPodAutoscalerInterface
{
    public function minReplicas(): int
    {
        return 1;
    }

    public function behavior(BehaviorConfigurator $behavior): void
    {
    }

    public function configureHorizontalPodAutoscaler(HorizontalPodAutoscaler $hpa): void
    {
    }

    public static function apiVersion(): string
    {
        return HorizontalPodAutoscaler::API_VERSION;
    }

    public static function kind(): string
    {
        return HorizontalPodAutoscaler::KIND;
    }
}
