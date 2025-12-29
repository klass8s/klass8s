<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Container;

interface ReplicasPolicyConfiguratorInterface
{
    public function configure(ReplicasPolicyRegistry $policies): void;
}
