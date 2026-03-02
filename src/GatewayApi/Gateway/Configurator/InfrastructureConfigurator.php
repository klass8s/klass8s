<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

final class InfrastructureConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function label(string $key, string $value): static
    {
        if (!isset($this->data['labels'])) {
            $this->data['labels'] = new \ArrayObject();
        }

        $this->data['labels'][$key] = $value;

        return $this;
    }

    public function annotation(string $key, string $value): static
    {
        if (!isset($this->data['annotations'])) {
            $this->data['annotations'] = new \ArrayObject();
        }

        $this->data['annotations'][$key] = $value;

        return $this;
    }

    public function parametersRef(string $group, string $kind, string $name): static
    {
        $this->data['parametersRef'] = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        return $this;
    }
}
