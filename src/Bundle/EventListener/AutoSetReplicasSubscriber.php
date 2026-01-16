<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\EventListener\Traits\EnsureMethodIsNotDeclaredInUserManifestTrait;
use Klass8s\App\AppInterface;
use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;
use Klass8s\Event\ManifestMethodEvent;
use Klass8s\Event\ProxyableMethodEventInterface;
use Klass8s\Util\PropertyAccessor;

class AutoSetReplicasSubscriber extends AbstractMethodSubscriber
{
    use EnsureMethodIsNotDeclaredInUserManifestTrait;

    protected function supports(ProxyableMethodEventInterface $event): bool
    {
        if ('replicas' !== $event->methodName()) {
            return false;
        }

        $manifest = $event->proxyable();

        return $manifest instanceof DeploymentInterface || $manifest instanceof StatefulSetInterface;
    }

    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        /** @var DeploymentInterface|StatefulSetInterface $manifest */
        $manifest = $event->proxyable();
        if (!$manifest instanceof DeploymentInterface && !$manifest instanceof StatefulSetInterface) {
            throw new \InvalidArgumentException(sprintf('Manifest "%s" must implement either %s or %s, but implements neither.', $manifest::class, DeploymentInterface::class, StatefulSetInterface::class));
        }

        $replicas = $manifest->config['replicas'] ?? null;
        if ($replicas) {
            $this->ensureMethodIsNotDeclaredInUserManifest($manifest, 'replicas');
            $event->setReturnValue($replicas);
        }
    }

    protected static function eventNames(): iterable
    {
        yield ManifestMethodEvent::NAME;
    }
}
