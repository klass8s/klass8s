<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

use Klass8s\GatewayApi\Enum\GRPCMethodMatchType;
use Klass8s\GatewayApi\Enum\HeaderMatchType;

final class GRPCRouteMatchConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function method(
        ?string $service = null,
        ?string $method = null,
        GRPCMethodMatchType $type = GRPCMethodMatchType::Exact,
    ): static {
        $this->data['method'] = array_filter([
            'type' => $type->value,
            'service' => $service,
            'method' => $method,
        ], static fn (mixed $v): bool => null !== $v);

        return $this;
    }

    public function header(string $name, string $value, HeaderMatchType $type = HeaderMatchType::Exact): static
    {
        if (!isset($this->data['headers'])) {
            $this->data['headers'] = [];
        }

        $this->data['headers'][] = [
            'type' => $type->value,
            'name' => $name,
            'value' => $value,
        ];

        return $this;
    }
}
