<?php

declare(strict_types=1);

namespace Klass8s\Event;

interface ProxyableMethodCalledEventInterface extends ProxyableMethodEventInterface
{
    public function returnedValue(): mixed;
}
