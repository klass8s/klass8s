<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

use Klass8s\App\AppInterface;

final class HTTPRouteRulesConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function add(): HTTPRouteRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $this->data[] = $ruleData;

        return new HTTPRouteRuleConfigurator($ruleData, $this->app);
    }
}
