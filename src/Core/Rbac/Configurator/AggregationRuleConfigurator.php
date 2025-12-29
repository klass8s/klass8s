<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac\Configurator;

use K8Schema\Api\Rbac\V1\AggregationRule;
use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\LabelSelector;
use Klass8s\Core\LabelSelector\SelectorConfigurator;

readonly class AggregationRuleConfigurator
{
    public function __construct(private AggregationRule $rule)
    {
    }

    public function addClusterRoleSelector(): SelectorConfigurator
    {
        $selector = new LabelSelector();
        $this->rule->clusterRoleSelectors()->add($selector);

        return new SelectorConfigurator($selector);
    }
}
