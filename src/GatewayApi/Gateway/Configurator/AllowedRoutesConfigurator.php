<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\GatewayApi\Enum\RouteNamespaceFrom;

final class AllowedRoutesConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function fromSameNamespace(): static
    {
        return $this->fromNamespaces(RouteNamespaceFrom::Same);
    }

    public function fromAllNamespaces(): static
    {
        return $this->fromNamespaces(RouteNamespaceFrom::All);
    }

    /**
     * @param array<string, string> $matchLabels
     */
    public function fromSelector(array $matchLabels): static
    {
        $this->fromNamespaces(RouteNamespaceFrom::Selector);
        $this->data['namespaces']['selector'] = ['matchLabels' => $matchLabels];

        return $this;
    }

    /**
     * @param list<array{group?: string, kind: string}> $kinds
     */
    public function allowKinds(array $kinds): static
    {
        $this->data['kinds'] = array_map(
            static fn (array $kind): array => array_filter([
                'group' => $kind['group'] ?? 'gateway.networking.k8s.io',
                'kind' => $kind['kind'],
            ]),
            $kinds,
        );

        return $this;
    }

    public function allowHTTPRoutes(): static
    {
        return $this->allowKinds([['kind' => 'HTTPRoute']]);
    }

    public function allowGRPCRoutes(): static
    {
        return $this->allowKinds([['kind' => 'GRPCRoute']]);
    }

    private function fromNamespaces(RouteNamespaceFrom $from): static
    {
        if (!isset($this->data['namespaces'])) {
            $this->data['namespaces'] = new \ArrayObject();
        }
        $this->data['namespaces']['from'] = $from->value;

        return $this;
    }
}
