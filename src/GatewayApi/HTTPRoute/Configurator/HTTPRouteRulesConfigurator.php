<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

final class HTTPRouteRulesConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(): HTTPRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data[] = $ruleData;

        return new HTTPRouteRuleConfigurator($ruleData);
    }
}
