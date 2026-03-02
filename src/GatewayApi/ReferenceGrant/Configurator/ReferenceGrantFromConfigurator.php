<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant\Configurator;

final class ReferenceGrantFromConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(string $group, string $kind, string $namespace): static
    {
        $this->data[] = [
            'group' => $group,
            'kind' => $kind,
            'namespace' => $namespace,
        ];

        return $this;
    }

    public function allowHTTPRoutes(string $namespace): static
    {
        return $this->add('gateway.networking.k8s.io', 'HTTPRoute', $namespace);
    }

    public function allowGRPCRoutes(string $namespace): static
    {
        return $this->add('gateway.networking.k8s.io', 'GRPCRoute', $namespace);
    }

    public function allowGateways(string $namespace): static
    {
        return $this->add('gateway.networking.k8s.io', 'Gateway', $namespace);
    }
}
