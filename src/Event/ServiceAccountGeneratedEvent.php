<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Core\V1\ServiceAccount;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\ServiceAccount\ServiceAccountInterface;
use Klass8s\Manifest\ManifestInterface;

readonly class ServiceAccountGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.service_account';

    public function __construct(
        private ServiceAccountInterface $manifest,
        private ServiceAccount $apiResource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): ManifestInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->apiResource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }

    public function serviceAccount(): ServiceAccount
    {
        return $this->apiResource;
    }
}
