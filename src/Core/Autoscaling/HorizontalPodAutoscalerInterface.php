<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling;

use K8Schema\Api\Autoscaling\V2\CrossVersionObjectReference;
use K8Schema\Api\Autoscaling\V2\HorizontalPodAutoscaler;
use Klass8s\Core\Autoscaling\Configurator\BehaviorConfigurator;
use Klass8s\Core\Autoscaling\Configurator\MetricsConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\VersionedManifestClassReference;

interface HorizontalPodAutoscalerInterface extends ManifestInterface
{
    public function minReplicas(): int;
    public function maxReplicas(): int;
    public function behavior(BehaviorConfigurator $behavior): void;
    public function metrics(MetricsConfigurator $metrics): void;
    public function scaleTargetRef(): VersionedManifestClassReference|CrossVersionObjectReference;
    public function configureHorizontalPodAutoscaler(HorizontalPodAutoscaler $hpa): void;
}
