<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant\Configurator;

final class ReferenceGrantToConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(string $group, string $kind, ?string $name = null): static
    {
        $entry = [
            'group' => $group,
            'kind' => $kind,
        ];

        if (null !== $name) {
            $entry['name'] = $name;
        }

        $this->data[] = $entry;

        return $this;
    }

    public function allowServices(?string $name = null): static
    {
        return $this->add('', 'Service', $name);
    }

    public function allowSecrets(?string $name = null): static
    {
        return $this->add('', 'Secret', $name);
    }

    public function allowConfigMaps(?string $name = null): static
    {
        return $this->add('', 'ConfigMap', $name);
    }
}
