<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant\Configurator;

use Klass8s\Collection\GenericList;

final readonly class ReferenceGrantFromConfigurator
{
    public function __construct(private GenericList $data)
    {
    }

    public function add(string $group, string $kind, string $namespace): self
    {
        $this->data->add([
            'group' => $group,
            'kind' => $kind,
            'namespace' => $namespace,
        ]);

        return $this;
    }

    public function allowHTTPRoutes(string $namespace): self
    {
        return $this->add('gateway.networking.k8s.io', 'HTTPRoute', $namespace);
    }

    public function allowGRPCRoutes(string $namespace): self
    {
        return $this->add('gateway.networking.k8s.io', 'GRPCRoute', $namespace);
    }

    public function allowGateways(string $namespace): self
    {
        return $this->add('gateway.networking.k8s.io', 'Gateway', $namespace);
    }
}
