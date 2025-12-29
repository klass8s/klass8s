<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Core\ManifestInterface;
use Proximity\ProxyInterface;

class ManifestMethodCalledEvent extends ManifestMethodEvent implements ProxyableMethodCalledEventInterface
{
    public const string NAME = 'klass8s.manifest.method_called';

    public function __construct(ManifestInterface&ProxyInterface $proxyable, string $methodName, array $methodParams, private readonly mixed $returnedValue)
    {
        parent::__construct($proxyable, $methodName, $methodParams);
    }

    public function returnedValue(): mixed
    {
        return $this->returnedValue;
    }
}
