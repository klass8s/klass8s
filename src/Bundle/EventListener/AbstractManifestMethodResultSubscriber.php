<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ManifestMethodCalledEvent;

abstract class AbstractManifestMethodResultSubscriber extends AbstractMethodResultSubscriber
{
    protected static function eventNames(): iterable
    {
        return [ManifestMethodCalledEvent::NAME];
    }
}
