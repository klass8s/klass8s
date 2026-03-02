<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;

final readonly class GRPCRouteRulesConfigurator
{
    public function __construct(
        private GenericList $data,
        private AppInterface $app,
    ) {
    }

    public function add(): GRPCRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data->add($ruleData);

        return new GRPCRouteRuleConfigurator($ruleData, $this->app);
    }
}
