<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\EnvManagement\Attribute\BeforeMethod;
use Klass8s\Bundle\EventListener\Traits\ApplyWrappersTrait;
use Klass8s\Event\ContainerMethodEvent;
use Klass8s\Event\ManifestMethodEvent;
use Klass8s\Event\ProxyableMethodEventInterface;

class BeforeMethodSubscriber extends AbstractMethodSubscriber
{
    use ApplyWrappersTrait;

    private const NO_RETURN_VALUE = 'BEFORE-METHOD-SUBSCRIBER-NO-RETURN-VALUE';

    public function __construct(private string $env)
    {
    }

    protected function supports(ProxyableMethodEventInterface $event): bool
    {
        return true;
    }

    /**
     * @throws \ReflectionException
     */
    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        $returnValue = self::NO_RETURN_VALUE;

        $this->applyWrappers(
            proxyable: $event->proxyable(),
            methodName: $event->methodName(),
            params: $event->methodParams(),
            attributeClass: BeforeMethod::class,
            returnValue: $returnValue,
        );

        if (self::NO_RETURN_VALUE !== $returnValue) {
            $event->setReturnValue($returnValue);
        }
    }

    protected static function eventNames(): iterable
    {
        return [
            ManifestMethodEvent::NAME,
            ContainerMethodEvent::NAME,
        ];
    }
}
