<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

final class HTTPRouteRuleConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function name(string $name): static
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function match(): HTTPRouteMatchConfigurator
    {
        if (!isset($this->data['matches'])) {
            $this->data['matches'] = new \ArrayObject();
        }

        $matchData = new \ArrayObject();
        $this->data['matches'][] = $matchData;

        return new HTTPRouteMatchConfigurator($matchData);
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

    public function backendRef(): HTTPBackendRefConfigurator
    {
        if (!isset($this->data['backendRefs'])) {
            $this->data['backendRefs'] = new \ArrayObject();
        }

        $refData = new \ArrayObject();
        $this->data['backendRefs'][] = $refData;

        return new HTTPBackendRefConfigurator($refData);
    }

    public function timeout(string $duration): static
    {
        if (!isset($this->data['timeouts'])) {
            $this->data['timeouts'] = new \ArrayObject();
        }
        $this->data['timeouts']['request'] = $duration;

        return $this;
    }

    public function backendTimeout(string $duration): static
    {
        if (!isset($this->data['timeouts'])) {
            $this->data['timeouts'] = new \ArrayObject();
        }
        $this->data['timeouts']['backendRequest'] = $duration;

        return $this;
    }

    /**
     * @param list<int> $codes
     */
    public function retry(array $codes, int $attempts, ?string $backoff = null): static
    {
        $this->data['retry'] = array_filter([
            'codes' => $codes,
            'attempts' => $attempts,
            'backoff' => $backoff,
        ], static fn (mixed $v): bool => null !== $v);

        return $this;
    }
}
