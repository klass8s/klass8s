<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Prometheus;

use K8Schema\Collection\StringList;
use Klass8s\Monitoring\Prometheus\Configurator\EndpointsConfigurator;
use Klass8s\Monitoring\Prometheus\MonitorInterface;
use Klass8s\Monitoring\Prometheus\ServiceMonitor\ServiceMonitorInterface;

class ServiceMonitorMaker extends AbstractMonitorMaker
{
    protected function supportedClass(): string
    {
        return ServiceMonitorInterface::class;
    }

    protected function configureMonitor(MonitorInterface|ServiceMonitorInterface $manifest, \ArrayObject $data): void
    {
        $targetLabels = new StringList();
        $manifest->targetLabels($targetLabels);

        if ($targetLabels->count() > 0) {
            $data['spec']['targetLabels'] = $targetLabels;
        }

        $data['spec']['endpoints'] = new \ArrayObject();

        $endpoints = new EndpointsConfigurator($data['spec']['endpoints']);
        $manifest->endpoints($endpoints);
    }
}
