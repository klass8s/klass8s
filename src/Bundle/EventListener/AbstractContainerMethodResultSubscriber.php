<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ContainerMethodCalledEvent;

abstract class AbstractContainerMethodResultSubscriber extends AbstractMethodResultSubscriber
{
    protected static function eventNames(): iterable
    {
        return [ContainerMethodCalledEvent::NAME];
    }
}
