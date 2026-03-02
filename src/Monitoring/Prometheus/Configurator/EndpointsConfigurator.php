<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

use Klass8s\Collection\GenericList;

final readonly class EndpointsConfigurator
{
    public function __construct(private GenericList $data)
    {
    }

    public function add(): EndpointConfigurator
    {
        $endpointData = new \ArrayObject();
        $this->data->add($endpointData);

        return new EndpointConfigurator($endpointData);
    }
}
