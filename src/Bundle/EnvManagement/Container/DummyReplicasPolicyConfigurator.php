<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Container;

final readonly class DummyReplicasPolicyConfigurator implements ReplicasPolicyConfiguratorInterface
{
    public function configure(ReplicasPolicyRegistry $policies): void
    {
    }
}
