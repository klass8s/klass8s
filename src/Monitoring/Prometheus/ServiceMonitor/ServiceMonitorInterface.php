<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\ServiceMonitor;

use K8Schema\Collection\StringList;
use Klass8s\Monitoring\Prometheus\Configurator\EndpointsConfigurator;
use Klass8s\Monitoring\Prometheus\MonitorInterface;

interface ServiceMonitorInterface extends MonitorInterface
{
    public function endpoints(EndpointsConfigurator $endpoints): void;
    public function targetLabels(StringList $labels): void;
}
