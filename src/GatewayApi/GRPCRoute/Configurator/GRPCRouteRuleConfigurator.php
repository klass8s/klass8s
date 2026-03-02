<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPBackendRefConfigurator;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteFilterConfigurator;

final class GRPCRouteRuleConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function name(string $name): static
    {
        $this->data['name'] = $name;

        return $this;
    }

    public function match(): GRPCRouteMatchConfigurator
    {
        if (!isset($this->data['matches'])) {
            $this->data['matches'] = new \ArrayObject();
        }

        $matchData = new \ArrayObject();
        $this->data['matches'][] = $matchData;

        return new GRPCRouteMatchConfigurator($matchData);
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

        return new HTTPBackendRefConfigurator($refData, $this->app);
    }
}
