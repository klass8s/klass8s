<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\ReferenceGrant\ReferenceGrantApiResource;
use Klass8s\GatewayApi\ReferenceGrant\ReferenceGrantInterface;

final readonly class ReferenceGrantGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.reference_grant';

    public function __construct(
        private ReferenceGrantInterface $manifest,
        private ReferenceGrantApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): ReferenceGrantInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function referenceGrant(): ReferenceGrantApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
