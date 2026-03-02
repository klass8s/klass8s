<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

use Klass8s\Collection\GenericList;

final readonly class RuleGroupsConfigurator
{
    public function __construct(private GenericList $data)
    {
    }

    public function add(string $name): RuleGroupConfigurator
    {
        $groupData = new \ArrayObject();
        $groupData['name'] = $name;
        $this->data->add($groupData);

        return new RuleGroupConfigurator($groupData);
    }
}
