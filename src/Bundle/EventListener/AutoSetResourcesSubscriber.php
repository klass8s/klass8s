<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\EventListener\Traits\EnsureMethodIsNotDeclaredInUserManifestTrait;
use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Container\Resources\ContainerResourcesInterface;
use Klass8s\Core\Container\Resources\CPU;
use Klass8s\Core\Container\Resources\Memory;
use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Core\Job\JobInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;
use Klass8s\Event\ContainerMethodEvent;
use Klass8s\Event\ManifestMethodEvent;
use Klass8s\Event\ProxyableMethodEventInterface;

class AutoSetResourcesSubscriber extends AbstractMethodSubscriber
{
    use EnsureMethodIsNotDeclaredInUserManifestTrait;

    protected function supports(ProxyableMethodEventInterface $event): bool
    {
        $manifest = $event->proxyable();
        $supportedWorkload = $manifest instanceof DeploymentInterface
            || $manifest instanceof JobInterface
            || $manifest instanceof StatefulSetInterface;

        return $supportedWorkload && $manifest instanceof ContainerInterface && 'resources' === $event->methodName();
    }

    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        /** @var DeploymentInterface|StatefulSetInterface $manifest */
        $manifest = $event->proxyable();
        if (!$manifest instanceof DeploymentInterface && !$manifest instanceof StatefulSetInterface) {
            throw new \InvalidArgumentException(sprintf('Manifest "%s" must implement either %s or %s, but implements neither.', $manifest::class, DeploymentInterface::class, StatefulSetInterface::class));
        }

        $config = $manifest->config['resources'] ?? [];
        $resources = $event->typedMethodParam('resources', ContainerResourcesInterface::class);

        $cpuRequests = $config['requests']['cpu'] ?? null;
        $cpuLimits = $config['limits']['cpu'] ?? null;
        if ($cpuRequests) {
            $resources->requestCPU(CPU::fromString($cpuRequests));
        }
        if ($cpuLimits) {
            $resources->limitCPU(CPU::fromString($cpuLimits));
        }

        $memoryRequests = $config['requests']['memory'] ?? null;
        $memoryLimits = $config['limits']['memory'] ?? null;
        if ($memoryRequests) {
            $resources->requestMemory(Memory::fromString($memoryRequests));
        }
        if ($memoryLimits) {
            $resources->limitMemory(Memory::fromString($memoryLimits));
        }

        $storageRequests = $config['requests']['storage'] ?? null;
        $storageLimits = $config['limits']['storage'] ?? null;
        if ($storageRequests) {
            $resources->requestStorage(Memory::fromString($storageRequests));
        }
        if ($storageLimits) {
            $resources->limitStorage(Memory::fromString($storageLimits));
        }

        if (!empty($config['requests']) || !empty($config['limits'])) {
            // We don't want ambiguous behavior, so only one way of specifying resources should be used:
            // if resources are automatically set from config, they should not be set by method
            $this->ensureMethodIsNotDeclaredInUserManifest($manifest, 'resources');
            $event->setReturnValue(null); // This is done to prevent method body
        }
    }

    protected static function eventNames(): iterable
    {
        yield ManifestMethodEvent::NAME;
        yield ContainerMethodEvent::NAME;
    }
}
