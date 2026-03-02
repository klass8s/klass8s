<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyApiResource;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyInterface;

final readonly class BackendTLSPolicyGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.backend_tls_policy';

    public function __construct(
        private BackendTLSPolicyInterface $manifest,
        private BackendTLSPolicyApiResource $resource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): BackendTLSPolicyInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }

    public function backendTLSPolicy(): BackendTLSPolicyApiResource
    {
        return $this->resource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
