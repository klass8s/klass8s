<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\EnvManagement\Attribute\AfterMethod;
use Klass8s\Bundle\EventListener\Traits\ApplyWrappersTrait;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Event\ProxyableMethodCalledEventInterface;

class AfterMethodSubscriber extends AbstractMethodResultSubscriber
{
    use ApplyWrappersTrait;

    private const string NO_RETURN_VALUE = '__after_method_no_return_value__';

    public function __construct(private readonly string $currentEnv)
    {
    }

    protected function supports(ProxyableMethodCalledEventInterface $event): bool
    {
        return true;
    }

    /**
     * @throws \ReflectionException
     */
    protected function afterMethod(ProxyableMethodCalledEventInterface $event): void
    {
        $returnValue = self::NO_RETURN_VALUE;

        $this->applyWrappers(
            proxyable: $event->proxyable(),
            methodName: $event->methodName(),
            params: $event->methodParams(),
            attributeClass: AfterMethod::class,
            returnValue: $returnValue,
        );

        if (self::NO_RETURN_VALUE !== $returnValue) {
            $event->setReturnValue($returnValue);
        }
    }

    protected static function eventNames(): iterable
    {
        return [
            ManifestMethodCalledEvent::NAME,
            ContainerMethodCalledEvent::NAME,
        ];
    }
}
