<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Prometheus;

use Klass8s\Monitoring\Prometheus\Configurator\EndpointsConfigurator;
use Klass8s\Monitoring\Prometheus\MonitorInterface;
use Klass8s\Monitoring\Prometheus\PodMonitor\PodMonitorInterface;

class PodMonitorMaker extends AbstractMonitorMaker
{
    protected function configureMonitor(MonitorInterface|PodMonitorInterface $manifest, \ArrayObject $data): void
    {
        $data['spec']['podMetricsEndpoints'] = new \ArrayObject();

        $endpoints = new EndpointsConfigurator($data['spec']['podMetricsEndpoints']);
        $manifest->podMetricsEndpoints($endpoints);
    }

    protected function supportsClass(): string
    {
        return PodMonitorInterface::class;
    }
}
