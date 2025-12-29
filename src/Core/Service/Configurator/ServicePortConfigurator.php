<?php

declare(strict_types=1);

namespace Klass8s\Core\Service\Configurator;

use K8Schema\Api\Core\V1\ServicePort;
use Klass8s\Core\Service\IPProtocol;

class ServicePortConfigurator
{
    private ServicePort $port;

    public function __construct(ServicePort $port)
    {
        $this->port = $port;
    }

    public function setProtocol(IPProtocol $protocol): self
    {
        $this->port->setProtocol($protocol->toString());

        return $this;
    }

    public function setName(string $name): self
    {
        $this->port->setName($name);

        return $this;
    }

    public function setNodePort(int $nodePort): self
    {
        $this->port->setNodePort($nodePort);

        return $this;
    }
}
