<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Container;

final readonly class DummyResourcePolicyConfigurator implements ResourcePolicyConfiguratorInterface
{
    public function configure(ResourcePolicyRegistry $policies): void
    {
    }
}
