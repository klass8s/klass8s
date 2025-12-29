<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ManifestMethodCalledEvent;

abstract class AbstractEnvAwareManifestMethodSubscriber extends AbstractEnvAwareMethodSubscriber
{
    protected static function eventNames(): iterable
    {
        return [ManifestMethodCalledEvent::NAME];
    }
}
