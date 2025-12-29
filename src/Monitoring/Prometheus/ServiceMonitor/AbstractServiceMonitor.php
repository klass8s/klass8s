<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\ServiceMonitor;

use K8Schema\Collection\StringList;
use Klass8s\Monitoring\Prometheus\AbstractMonitor;

abstract class AbstractServiceMonitor extends AbstractMonitor implements ServiceMonitorInterface
{
    public static function kind(): string
    {
        return 'ServiceMonitor';
    }

    public function targetLabels(StringList $labels): void
    {
    }
}
