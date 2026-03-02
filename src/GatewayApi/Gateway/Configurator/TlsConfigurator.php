<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\GatewayApi\Enum\TlsMode;

final class TlsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function mode(TlsMode $mode): static
    {
        $this->data['mode'] = $mode->value;

        return $this;
    }

    public function terminate(): static
    {
        return $this->mode(TlsMode::Terminate);
    }

    public function passthrough(): static
    {
        return $this->mode(TlsMode::Passthrough);
    }

    public function certificateRef(string $name, ?string $namespace = null, string $group = '', string $kind = 'Secret'): static
    {
        if (!isset($this->data['certificateRefs'])) {
            $this->data['certificateRefs'] = [];
        }

        $ref = array_filter([
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
            'namespace' => $namespace,
        ], static fn (mixed $v): bool => null !== $v);

        $this->data['certificateRefs'][] = $ref;

        return $this;
    }

    /**
     * @param array<string, string> $options
     */
    public function options(array $options): static
    {
        $this->data['options'] = $options;

        return $this;
    }
}
