<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Core\Container\ContainerInterface;
use Proximity\ProxyInterface;

class ContainerMethodCalledEvent extends ContainerMethodEvent implements ProxyableMethodCalledEventInterface
{
    public const string NAME = 'klass8s.container.method_called';

    public function __construct(
        ContainerInterface&ProxyInterface $proxyable,
        string $methodName,
        array $methodParams,
        private readonly mixed $returnedValue,
    ) {
        parent::__construct($proxyable, $methodName, $methodParams);
    }

    public function returnedValue(): mixed
    {
        return $this->returnedValue;
    }
}
