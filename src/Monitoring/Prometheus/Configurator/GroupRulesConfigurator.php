<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

use Klass8s\Collection\GenericList;

final readonly class GroupRulesConfigurator
{
    public function __construct(private GenericList $rules)
    {
    }

    public function add(int|string $expr): GroupRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $ruleData['expr'] = $expr;
        $this->rules->add($ruleData);

        return new GroupRuleConfigurator($ruleData);
    }
}
