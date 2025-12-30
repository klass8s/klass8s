<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\Metrics;

class MemoryTrigger extends AbstractStandardMetricTrigger
{
    protected static function type(): string
    {
        return 'memory';
    }
}
