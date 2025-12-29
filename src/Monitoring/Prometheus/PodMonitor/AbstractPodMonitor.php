<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\PodMonitor;

use Klass8s\Monitoring\Prometheus\AbstractMonitor;

abstract class AbstractPodMonitor extends AbstractMonitor implements PodMonitorInterface
{
    public static function kind(): string
    {
        return 'PodMonitor';
    }
}
