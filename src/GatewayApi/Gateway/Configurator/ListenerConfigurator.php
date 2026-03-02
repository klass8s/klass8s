<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\GatewayApi\Enum\ProtocolType;

final class ListenerConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function port(int $port): static
    {
        $this->data['port'] = $port;

        return $this;
    }

    public function protocol(ProtocolType $protocol): static
    {
        $this->data['protocol'] = $protocol->value;

        return $this;
    }

    public function hostname(string $hostname): static
    {
        $this->data['hostname'] = $hostname;

        return $this;
    }

    public function tls(): TlsConfigurator
    {
        if (!isset($this->data['tls'])) {
            $this->data['tls'] = new \ArrayObject();
        }

        return new TlsConfigurator($this->data['tls']);
    }

    public function allowedRoutes(): AllowedRoutesConfigurator
    {
        if (!isset($this->data['allowedRoutes'])) {
            $this->data['allowedRoutes'] = new \ArrayObject();
        }

        return new AllowedRoutesConfigurator($this->data['allowedRoutes']);
    }
}
