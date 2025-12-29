<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

final class GroupRulesConfigurator
{
    public function __construct(private \ArrayObject $rules)
    {
    }

    public function add(int|string $expr): GroupRuleConfigurator
    {
        $ruleData = new \ArrayObject();
        $ruleData['expr'] = $expr;
        $this->rules[] = $ruleData;

        return new GroupRuleConfigurator($ruleData);
    }
}
