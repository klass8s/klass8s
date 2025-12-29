<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ProxyableMethodCalledEventInterface;

class ReplicasMethodSubscriber extends AbstractEnvAwareManifestMethodSubscriber
{
    public function __construct(string $env, private readonly bool $deploymentReplicasPoliciesEnabled)
    {
        parent::__construct($env);
    }

    public function methodName(): string
    {
        return 'replicas';
    }

    protected function supports(ProxyableMethodCalledEventInterface $event): bool
    {
        return !$this->deploymentReplicasPoliciesEnabled;
    }
}
