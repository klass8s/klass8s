<?php

declare(strict_types=1);

namespace Klass8s\Proxy;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ContainerMethodEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Event\ManifestMethodEvent;
use Proximity\MethodsInterception\BodyInterceptionResult;
use Proximity\MethodsInterception\BodyInterceptorInterface;
use Proximity\MethodsInterception\InterceptionContext;
use Proximity\MethodsInterception\ResultInterceptorInterface;
use Proximity\ProxyInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class EventDispatcherBridgeInterceptor implements BodyInterceptorInterface, ResultInterceptorInterface
{
    /**
     * @var array<string, \ReflectionClass>
     */
    private array $reflectionsCache = [];

    public function __construct(private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public function beforeMethodBody(ProxyInterface $proxy, object $object, string $methodName, array $methodArgs): ?BodyInterceptionResult
    {
        $manifestInterfaceReflection = $this->getReflection(ManifestInterface::class);
        $containerInterfaceReflection = $this->getReflection(ContainerInterface::class);
        $manifestHasMethod = $manifestInterfaceReflection->hasMethod($methodName);
        $containerHasMethod = $containerInterfaceReflection->hasMethod($methodName);

        if ($manifestHasMethod && $containerHasMethod) {
            throw new \LogicException(sprintf('Both ManifestInterface and ContainerInterface have method "%s", which is forbidden.', $methodName));
        } elseif ($manifestHasMethod && $proxy instanceof ManifestInterface) {
            $event = new ManifestMethodEvent($proxy, $methodName, $methodArgs);
            $eventName = ManifestMethodEvent::NAME;
        } elseif ($containerHasMethod && $proxy instanceof ContainerInterface) {
            $event = new ContainerMethodEvent($proxy, $methodName, $methodArgs);
            $eventName = ContainerMethodEvent::NAME;
        } elseif ($proxy instanceof ManifestInterface) {
            $event = new ManifestMethodEvent($proxy, $methodName, $methodArgs);
            $eventName = ManifestMethodEvent::NAME;
        } elseif ($proxy instanceof ContainerInterface) {
            $event = new ContainerMethodEvent($proxy, $methodName, $methodArgs);
            $eventName = ContainerMethodEvent::NAME;
        } else {
            return null;
        }

        $this->dispatcher->dispatch($event, $eventName);
        if ($event->returnValueHasChanged()) {
            return new BodyInterceptionResult(true, $event->getReturnValue());
        }

        return null;
    }

    public function afterMethodBody(ProxyInterface $proxy, object $object, string $methodName, array $methodArgs, InterceptionContext $context): void
    {
        $manifestInterfaceReflection = $this->getReflection(ManifestInterface::class);
        $containerInterfaceReflection = $this->getReflection(ContainerInterface::class);

        $manifestHasMethod = $manifestInterfaceReflection->hasMethod($methodName);
        $containerHasMethod = $containerInterfaceReflection->hasMethod($methodName);
        if ($manifestHasMethod && $containerHasMethod) {
            throw new \LogicException(sprintf('Both ManifestInterface and ContainerInterface have method "%s", which is forbidden.', $methodName));
        } elseif ($manifestHasMethod && $proxy instanceof ManifestInterface) {
            $event = new ManifestMethodCalledEvent($proxy, $methodName, $methodArgs, $context->returnValue);
            $eventName = ManifestMethodCalledEvent::NAME;
        } elseif ($containerHasMethod && $proxy instanceof ContainerInterface) {
            $event = new ContainerMethodCalledEvent($proxy, $methodName, $methodArgs, $context->returnValue);
            $eventName = ContainerMethodCalledEvent::NAME;
        } elseif ($proxy instanceof ManifestInterface) {
            $event = new ManifestMethodCalledEvent($proxy, $methodName, $methodArgs, $context->returnValue);
            $eventName = ManifestMethodCalledEvent::NAME;
        } elseif ($proxy instanceof ContainerInterface) {
            $event = new ContainerMethodCalledEvent($proxy, $methodName, $methodArgs, $context->returnValue);
            $eventName = ContainerMethodCalledEvent::NAME;
        } else {
            return;
        }

        $this->dispatcher->dispatch($event, $eventName);
        if ($event->returnValueHasChanged()) {
            $context->returnValue = $event->getReturnValue();
        }
    }

    /**
     * @param class-string $className
     */
    private function getReflection(string $className): \ReflectionClass
    {
        return $this->reflectionsCache[$className] ??= self::createReflection($className);
    }

    /**
     * @param class-string $className
     */
    private static function createReflection(string $className): \ReflectionClass
    {
        return new \ReflectionClass($className);
    }
}
