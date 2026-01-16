<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Ports;

use K8Schema\Api\Core\V1\ContainerPort;
use Klass8s\Core\Service\IPProtocol;

class ContainerPortBuilder
{
    private ContainerPort $port;

    public function __construct(ContainerPort $port)
    {
        $this->port = $port;
    }

    public function setName(string $name): self
    {
        $this->port->setName($name);

        return $this;
    }

    public function setHostIP(string $hostIP): self
    {
        $this->port->setHostIP($hostIP);

        return $this;
    }

    public function setHostPort(int $port): self
    {
        $this->port->setHostPort($port);

        return $this;
    }

    public function setProtocol(IPProtocol $protocol): self
    {
        $this->port->setProtocol($protocol->value);

        return $this;
    }
}
