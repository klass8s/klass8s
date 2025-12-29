<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Persistence\PersistentVolumeClaimInterface;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Event\ProxyableMethodCalledEventInterface;

class ResourcesMethodSubscriber extends AbstractEnvAwareMethodSubscriber
{
    public function __construct(
        string $env,
        private readonly bool $containerResourcesPoliciesEnabled,
    ) {
        parent::__construct($env);
    }

    protected function methodName(): string
    {
        return 'resources';
    }

    protected function supports(ProxyableMethodCalledEventInterface $event): bool
    {
        // This subscriber must not work when container resources policies are used, since this is alternative approach to setting resources.
        if ($this->containerResourcesPoliciesEnabled) {
            return false;
        }

        $proxyable = $event->proxyable();

        return $proxyable instanceof ContainerInterface || $proxyable instanceof PersistentVolumeClaimInterface;
    }

    protected static function eventNames(): iterable
    {
        return [
            ManifestMethodCalledEvent::NAME,
            ContainerMethodCalledEvent::NAME,
        ];
    }
}
