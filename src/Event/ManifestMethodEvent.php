<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Manifest\ManifestInterface;
use Proximity\ProxyInterface;

class ManifestMethodEvent extends AbstractProxyableMethodEvent
{
    public const NAME = 'klass8s.manifest.before_method';

    public function __construct(ManifestInterface&ProxyInterface $proxyable, string $methodName, array $methodParams)
    {
        parent::__construct($proxyable, $methodName, $methodParams);
    }

    public function manifest(): ManifestInterface
    {
        return $this->proxyable;
    }
}
