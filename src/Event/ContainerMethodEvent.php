<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Proxy\ProxyableInterface;
use Proximity\ProxyInterface;

class ContainerMethodEvent extends AbstractProxyableMethodEvent
{
    public const string NAME = 'klass8s.container.before_method';

    protected readonly ProxyableInterface $proxyable;

    public function __construct(ContainerInterface&ProxyInterface $proxyable, string $methodName, array $methodParams)
    {
        parent::__construct($proxyable, $methodName, $methodParams);
    }

    public function container(): ContainerInterface&ProxyInterface
    {
        return $this->proxyable;
    }
}
