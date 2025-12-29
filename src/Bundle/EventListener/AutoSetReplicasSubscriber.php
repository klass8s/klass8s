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
        $manifest = $event->proxyable();
        $supportedWorkload = $manifest instanceof DeploymentInterface || $manifest instanceof StatefulSetInterface;

        return $supportedWorkload && 'replicas' === $event->methodName();
    }

    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        /** @var DeploymentInterface|StatefulSetInterface $manifest */
        $manifest = $event->proxyable();

        /** @var AppInterface $app */
        $app = PropertyAccessor::get($manifest, 'app');
        $replicas = $app->manifestConfig($manifest::shortName())['replicas'] ?? null;
        if ($replicas) {
            $this->ensureMethodIsNotDeclaredInUserManifest($manifest, 'replicas', $app);
            $event->setReturnValue($replicas);
        }
    }

    protected static function eventNames(): iterable
    {
        yield ManifestMethodEvent::NAME;
    }
}
