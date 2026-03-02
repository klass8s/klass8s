<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\GRPCRoute\GRPCRouteApiResource;
use Klass8s\GatewayApi\GRPCRoute\GRPCRouteInterface;

final readonly class GRPCRouteGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.grpc_route';

    public function __construct(
        private GRPCRouteInterface $manifest,
        private GRPCRouteApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): GRPCRouteInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function grpcRoute(): GRPCRouteApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
