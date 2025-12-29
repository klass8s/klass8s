<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

final class EndpointsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(): EndpointConfigurator
    {
        $endpointData = new \ArrayObject();
        $this->data[] = $endpointData;

        return new EndpointConfigurator($endpointData);
    }
}
