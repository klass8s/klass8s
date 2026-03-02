<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

final class HTTPBackendRefConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function service(string $name, int $port): static
    {
        $this->data['name'] = $name;
        $this->data['port'] = $port;

        return $this;
    }

    public function group(string $group): static
    {
        $this->data['group'] = $group;

        return $this;
    }

    public function kind(string $kind): static
    {
        $this->data['kind'] = $kind;

        return $this;
    }

    public function namespace(string $namespace): static
    {
        $this->data['namespace'] = $namespace;

        return $this;
    }

    public function weight(int $weight): static
    {
        $this->data['weight'] = $weight;

        return $this;
    }

    public function filter(): HTTPRouteFilterConfigurator
    {
        if (!isset($this->data['filters'])) {
            $this->data['filters'] = new \ArrayObject();
        }

        $filterData = new \ArrayObject();
        $this->data['filters'][] = $filterData;

        return new HTTPRouteFilterConfigurator($filterData);
    }
}
