<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;

final class HTTPRouteRuleConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private readonly AppInterface $app,
    ) {
    }

    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function match(): HTTPRouteMatchConfigurator
    {
        if (!isset($this->data['matches'])) {
            $this->data['matches'] = new GenericList();
        }

        $matchData = new \ArrayObject();
        $this->data['matches']->add($matchData);

        return new HTTPRouteMatchConfigurator($matchData);
    }

    public function filter(): HTTPRouteFilterConfigurator
    {
        if (!isset($this->data['filters'])) {
            $this->data['filters'] = new GenericList();
        }

        $filterData = new \ArrayObject();
        $this->data['filters']->add($filterData);

        return new HTTPRouteFilterConfigurator($filterData);
    }

    public function backendRef(): HTTPBackendRefConfigurator
    {
        if (!isset($this->data['backendRefs'])) {
            $this->data['backendRefs'] = new GenericList();
        }

        $refData = new \ArrayObject();
        $this->data['backendRefs']->add($refData);

        return new HTTPBackendRefConfigurator($refData, $this->app);
    }

    public function timeout(string $duration): self
    {
        if (!isset($this->data['timeouts'])) {
            $this->data['timeouts'] = new \ArrayObject();
        }
        $this->data['timeouts']['request'] = $duration;

        return $this;
    }

    public function backendTimeout(string $duration): self
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
    public function retry(array $codes, int $attempts, ?string $backoff = null): self
    {
        $this->data['retry'] = array_filter([
            'codes' => $codes,
            'attempts' => $attempts,
            'backoff' => $backoff,
        ], static fn (mixed $v): bool => null !== $v);

        return $this;
    }
}
