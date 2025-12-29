<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ManifestMethodEvent;

abstract class AbstractManifestMethodSubscriber extends AbstractMethodSubscriber
{
    protected static function eventNames(): iterable
    {
        return [ManifestMethodEvent::NAME];
    }
}
