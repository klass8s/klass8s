<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Core\ProxyableInterface;

interface ProxyableMethodEventInterface
{
    public function proxyable(): ProxyableInterface;
    public function methodName(): string;
    public function methodParams(): array;
    public function getReturnValue(): mixed;
    public function setReturnValue(mixed $returnValue): void;
    public function returnValueHasChanged(): bool;
}
