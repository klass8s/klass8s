<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

use Klass8s\App\AppInterface;

final class GRPCRouteRulesConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function add(): GRPCRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data[] = $ruleData;

        return new GRPCRouteRuleConfigurator($ruleData, $this->app);
    }
}
