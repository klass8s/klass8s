<?php

declare(strict_types=1);

namespace Klass8s\Core\StatefulSet\UpdateStrategy;

use K8Schema\Api\Apps\V1\RollingUpdateStatefulSetStrategy;

readonly class RollingUpdateStrategyConfigurator
{
    public function __construct(private RollingUpdateStatefulSetStrategy $rollingUpdate)
    {
    }

    public function setPartition(int $partition): void
    {
        $this->rollingUpdate->setPartition($partition);
    }
}
