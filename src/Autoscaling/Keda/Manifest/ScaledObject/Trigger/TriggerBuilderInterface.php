<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger;

use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Core\Autoscaling\Metric\TargetType;

interface TriggerBuilderInterface
{
    public function build(): Trigger;
    public function metricType(TargetType $type): static;
    public function useCachedMetrics(bool $value): static;
}
