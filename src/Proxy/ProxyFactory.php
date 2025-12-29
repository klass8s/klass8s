<?php

declare(strict_types=1);

namespace Klass8s\Proxy;

use Proximity\MethodsInterception\BodyInterceptorInterface;
use Proximity\MethodsInterception\ResultInterceptorInterface;
use Proximity\ProxyInterface;
use Proximity\ProxyOptions;

class ProxyFactory
{
    private array $bodyInterceptors;
    private array $resultInterceptors;

    /**
     * @param BodyInterceptorInterface[] $bodyInterceptors
     * @param ResultInterceptorInterface[] $resultInterceptors
     */
    public function __construct(
        protected \Proximity\ProxyFactory $proxyFactory,
        iterable $bodyInterceptors,
        iterable $resultInterceptors,
    ) {
        $this->bodyInterceptors = $bodyInterceptors instanceof \Traversable ? iterator_to_array($bodyInterceptors) : $bodyInterceptors;
        $this->resultInterceptors = $resultInterceptors instanceof \Traversable ? iterator_to_array($resultInterceptors) : $resultInterceptors;
    }

    public function makeProxy(object $object): ProxyInterface
    {
        $options = new ProxyOptions(
            defaultBodyInterceptors: $this->bodyInterceptors,
            defaultResultInterceptors: $this->resultInterceptors
        );

        return $this->proxyFactory->proxy($object, $options);
    }
}
