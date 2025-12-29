<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Ports;

use K8Schema\Api\Core\V1\ContainerPort;
use K8Schema\Api\Core\V1\ContainerPortList;

class PortsConfigurator
{
    private ContainerPortList $ports;

    public function __construct(ContainerPortList $ports)
    {
        $this->ports = $ports;
    }

    public function add(int $containerPort): ContainerPortBuilder
    {
        $port = new ContainerPort($containerPort);
        $this->ports->add($port);

        return new ContainerPortBuilder($port);
    }
}
