<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\PodMonitor;

use Klass8s\Monitoring\Prometheus\Configurator\EndpointsConfigurator;
use Klass8s\Monitoring\Prometheus\MonitorInterface;

interface PodMonitorInterface extends MonitorInterface
{
    public function podMetricsEndpoints(EndpointsConfigurator $endpoints): void;
}
