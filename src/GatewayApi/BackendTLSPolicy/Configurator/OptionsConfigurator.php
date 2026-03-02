<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

final class OptionsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function set(string $key, string $value): static
    {
        $this->data[$key] = $value;

        return $this;
    }
}
