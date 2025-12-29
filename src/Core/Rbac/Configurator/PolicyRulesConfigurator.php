<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac\Configurator;

use K8Schema\Api\Rbac\V1\PolicyRule;
use K8Schema\Api\Rbac\V1\PolicyRuleList;

readonly class PolicyRulesConfigurator
{
    public function __construct(private PolicyRuleList $rules)
    {
    }

    public function add(): PolicyRuleConfigurator
    {
        $rule = new PolicyRule();
        $this->rules->add($rule);

        return new PolicyRuleConfigurator($rule);
    }
}
