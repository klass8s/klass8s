<?php

declare(strict_types=1);

namespace Klass8s\Core\Service\Configurator;

use K8Schema\Api\Core\V1\ServicePort;
use K8Schema\Api\Core\V1\ServicePortList;

class ServicePortsConfigurator
{
    private ServicePortList $ports;

    public function __construct(ServicePortList $ports)
    {
        $this->ports = $ports;
    }

    public function add(int $servicePort, int|string $targetPort): ServicePortConfigurator
    {
        $port = new ServicePort($servicePort);
        $port->setTargetPort($targetPort);
        $this->ports->add($port);

        return new ServicePortConfigurator($port);
    }
}
