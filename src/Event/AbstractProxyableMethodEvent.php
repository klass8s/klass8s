<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Proxy\ProxyableInterface;

class AbstractProxyableMethodEvent implements ProxyableMethodEventInterface
{
    protected mixed $returnValue = null;
    protected bool $returnValueHasChanged = false;

    public function __construct(
        protected readonly ProxyableInterface $proxyable,
        protected readonly string $methodName,
        protected readonly array $methodParams,
    ) {
    }

    public function proxyable(): ProxyableInterface
    {
        return $this->proxyable;
    }

    public function methodName(): string
    {
        return $this->methodName;
    }

    public function methodParams(): array
    {
        return $this->methodParams;
    }

    public function methodParam(string $name): mixed
    {
        if (!array_key_exists($name, $this->methodParams)) {
            throw new \InvalidArgumentException(sprintf('Method parameter "%s" does not exist.', $name));
        }

        return $this->methodParams[$name];
    }

    public function typedMethodParam(string $paramName, string $paramClass): object
    {
        $param = $this->methodParam($paramName);
        if (!$param instanceof $paramClass) {
            throw new \InvalidArgumentException(sprintf('Method parameter "%s" must be an instance of "%s", "%s" given.', $paramName, $paramClass, get_debug_type($param)));
        }

        return $param;
    }

    public function getReturnValue(): mixed
    {
        return $this->returnValue;
    }

    public function returnValueHasChanged(): bool
    {
        return $this->returnValueHasChanged;
    }

    public function setReturnValue(mixed $returnValue): void
    {
        $this->returnValue = $returnValue;
        $this->returnValueHasChanged = true;
    }
}
