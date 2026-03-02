<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Configurator;

final class ParentRefConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
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

    public function sectionName(string $sectionName): static
    {
        $this->data['sectionName'] = $sectionName;

        return $this;
    }

    public function port(int $port): static
    {
        $this->data['port'] = $port;

        return $this;
    }
}
