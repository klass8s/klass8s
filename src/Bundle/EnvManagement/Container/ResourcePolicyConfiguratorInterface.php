<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Container;

interface ResourcePolicyConfiguratorInterface
{
    public function configure(ResourcePolicyRegistry $policies): void;
}
