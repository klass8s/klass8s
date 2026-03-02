<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\HTTPRoute\HTTPRouteApiResource;
use Klass8s\GatewayApi\HTTPRoute\HTTPRouteInterface;

final readonly class HTTPRouteGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.http_route';

    public function __construct(
        private HTTPRouteInterface $manifest,
        private HTTPRouteApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): HTTPRouteInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function httpRoute(): HTTPRouteApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
