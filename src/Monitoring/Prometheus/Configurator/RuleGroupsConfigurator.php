<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

final class RuleGroupsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(string $name): RuleGroupConfigurator
    {
        $groupData = new \ArrayObject();
        $groupData['name'] = $name;
        $this->data[] = $groupData;

        return new RuleGroupConfigurator($groupData);
    }
}
