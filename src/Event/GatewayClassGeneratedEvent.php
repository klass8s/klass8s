<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\GatewayClass\GatewayClassApiResource;
use Klass8s\GatewayApi\GatewayClass\GatewayClassInterface;

final readonly class GatewayClassGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.gateway_class';

    public function __construct(
        private GatewayClassInterface $manifest,
        private GatewayClassApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): GatewayClassInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function gatewayClass(): GatewayClassApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
