<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\EnvManagement\Container\ResourcePolicyApplier;
use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Container\Resources\ResourcesConfigurator;
use Klass8s\Core\Persistence\PersistentVolumeClaimInterface;
use Klass8s\Core\Persistence\PvcResourcesConfigurator;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Event\ProxyableMethodCalledEventInterface;

class ResourcesPoliciesSubscriber extends AbstractMethodResultSubscriber
{
    public function __construct(
        private readonly ResourcePolicyApplier $resourcePolicyApplier,
        private readonly bool $containerResourcesPoliciesEnabled,
        private readonly string $containerResourcesPoliciesEnv,
    ) {
    }

    protected function supports(ProxyableMethodCalledEventInterface $event): bool
    {
        $proxyable = $event->proxyable();

        return $this->containerResourcesPoliciesEnabled
            && ($proxyable instanceof ContainerInterface || $proxyable instanceof PersistentVolumeClaimInterface)
            && 'resources' === $event->methodName();
    }

    protected function afterMethod(ProxyableMethodCalledEventInterface $event): void
    {
        /** @var ContainerInterface|PersistentVolumeClaimInterface $proxyable */
        $proxyable = $event->proxyable();
        /** @var ResourcesConfigurator|PvcResourcesConfigurator $resources */
        $resources = $event->methodParams()['resources'];
        $this->resourcePolicyApplier->apply($proxyable, $resources, $this->containerResourcesPoliciesEnv);
    }

    protected static function eventNames(): iterable
    {
        return [
            ManifestMethodCalledEvent::NAME,
            ContainerMethodCalledEvent::NAME,
        ];
    }
}
