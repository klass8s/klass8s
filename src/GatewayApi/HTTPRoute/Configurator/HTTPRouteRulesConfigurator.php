<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;

final readonly class HTTPRouteRulesConfigurator
{
    public function __construct(
        private GenericList $data,
        private AppInterface $app,
    ) {
    }

    public function add(): HTTPRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data->add($ruleData);

        return new HTTPRouteRuleConfigurator($ruleData, $this->app);
    }
}
