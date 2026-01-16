<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Proxy\ProxyableInterface;

interface ProxyableMethodEventInterface
{
    public function proxyable(): ProxyableInterface;
    public function methodName(): string;
    public function methodParams(): array;

    public function methodParam(string $name): mixed;

    /**
     * @template T of object
     *
     * @param string $paramName
     * @param class-string<T> $paramClass
     * @return T
     */
    public function typedMethodParam(string $paramName, string $paramClass): object;

    public function getReturnValue(): mixed;
    public function setReturnValue(mixed $returnValue): void;
    public function returnValueHasChanged(): bool;
}
