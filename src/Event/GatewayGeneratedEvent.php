<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\Gateway\GatewayApiResource;
use Klass8s\GatewayApi\Gateway\GatewayInterface;

final readonly class GatewayGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.gateway';

    public function __construct(
        private GatewayInterface $manifest,
        private GatewayApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): GatewayInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function gateway(): GatewayApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
