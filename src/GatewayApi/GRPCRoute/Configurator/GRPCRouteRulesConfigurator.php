<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

final class GRPCRouteRulesConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(): GRPCRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data[] = $ruleData;

        return new GRPCRouteRuleConfigurator($ruleData);
    }
}
