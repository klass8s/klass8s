<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\Collection\GenericList;
use Klass8s\GatewayApi\Enum\AddressType;

final readonly class AddressesConfigurator
{
    public function __construct(private GenericList $data)
    {
    }

    public function add(AddressType $type, string $value): self
    {
        $this->data->add([
            'type' => $type->value,
            'value' => $value,
        ]);

        return $this;
    }

    public function addIPAddress(string $value): self
    {
        return $this->add(AddressType::IPAddress, $value);
    }

    public function addHostname(string $value): self
    {
        return $this->add(AddressType::Hostname, $value);
    }

    public function addNamedAddress(string $value): self
    {
        return $this->add(AddressType::NamedAddress, $value);
    }
}
