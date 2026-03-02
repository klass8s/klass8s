<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant\Configurator;

use Klass8s\Collection\GenericList;

final readonly class ReferenceGrantToConfigurator
{
    public function __construct(private GenericList $data)
    {
    }

    public function add(string $group, string $kind, ?string $name = null): self
    {
        $entry = [
            'group' => $group,
            'kind' => $kind,
        ];

        if (null !== $name) {
            $entry['name'] = $name;
        }

        $this->data->add($entry);

        return $this;
    }

    public function allowServices(?string $name = null): self
    {
        return $this->add('', 'Service', $name);
    }

    public function allowSecrets(?string $name = null): self
    {
        return $this->add('', 'Secret', $name);
    }

    public function allowConfigMaps(?string $name = null): self
    {
        return $this->add('', 'ConfigMap', $name);
    }
}
