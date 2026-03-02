<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\GatewayApi\Enum\AddressType;

final class AddressesConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(AddressType $type, string $value): static
    {
        $this->data[] = [
            'type' => $type->value,
            'value' => $value,
        ];

        return $this;
    }

    public function addIPAddress(string $value): static
    {
        return $this->add(AddressType::IPAddress, $value);
    }

    public function addHostname(string $value): static
    {
        return $this->add(AddressType::Hostname, $value);
    }

    public function addNamedAddress(string $value): static
    {
        return $this->add(AddressType::NamedAddress, $value);
    }
}
