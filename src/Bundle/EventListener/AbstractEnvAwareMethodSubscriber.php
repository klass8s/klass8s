<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ProxyableMethodCalledEventInterface;
use Klass8s\Util\ReflectionUtil;
use Klass8s\Util\Str;
use Proximity\ProxyInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

abstract class AbstractEnvAwareMethodSubscriber extends AbstractMethodResultSubscriber
{
    abstract protected function methodName(): string;

    public function __construct(#[Autowire(param: 'kernel.environment')] protected string $env)
    {
    }

    protected function afterMethod(ProxyableMethodCalledEventInterface $event): void
    {
        $class = new \ReflectionObject($event->proxyable());
        if ($class->implementsInterface(ProxyInterface::class)) {
            $class = $class->getParentClass();
        }
        $methodName = $event->methodName();
        if ($this->methodName() !== $methodName || !$class->hasMethod($methodName)) {
            return;
        }
        if (!$this->supports($event)) {
            return;
        }
        $envAwareMethodName = $this->methodName().Str::asClassName($this->env);
        if (!$class->hasMethod($envAwareMethodName)) {
            return;
        }

        $originalMethod = $class->getMethod($methodName);
        $envAwareMethod = $class->getMethod($envAwareMethodName);
        if (!ReflectionUtil::sameSignature($originalMethod, $envAwareMethod)) {
            throw new \LogicException(sprintf('Class "%s" has env-aware version of method "%s": method "%s", but signatures does not match.', $class->getName(), $methodName, $envAwareMethodName));
        }

        $returnType = $envAwareMethod->getReturnType();
        $returned = $envAwareMethod->invoke($event->proxyable(), ...$event->methodParams());
        if ($this->replacesReturnValue()
            && $envAwareMethod->hasReturnType()
            && 'void' !== $returnType->getName()
            && (null !== $returned || ($returnType?->allowsNull() ?? true))
        ) {
            $event->setReturnValue($returned);
        }
    }

    protected function replacesReturnValue(): bool
    {
        return true;
    }

    protected function supports(ProxyableMethodCalledEventInterface $event): bool
    {
        return $event->methodName() === $this->methodName();
    }
}
