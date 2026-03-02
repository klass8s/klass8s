<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPBackendRefConfigurator;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteFilterConfigurator;

final class GRPCRouteRuleConfigurator
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

    public function match(): GRPCRouteMatchConfigurator
    {
        if (!isset($this->data['matches'])) {
            $this->data['matches'] = new GenericList();
        }

        $matchData = new \ArrayObject();
        $this->data['matches']->add($matchData);

        return new GRPCRouteMatchConfigurator($matchData);
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
}
