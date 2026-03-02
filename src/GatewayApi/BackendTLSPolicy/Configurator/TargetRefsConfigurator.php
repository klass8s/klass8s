<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

final class TargetRefsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function addService(string $name, ?string $sectionName = null): static
    {
        return $this->add('', 'Service', $name, $sectionName);
    }

    public function add(string $group, string $kind, string $name, ?string $sectionName = null): static
    {
        $ref = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        if (null !== $sectionName) {
            $ref['sectionName'] = $sectionName;
        }

        $this->data[] = $ref;

        return $this;
    }
}
