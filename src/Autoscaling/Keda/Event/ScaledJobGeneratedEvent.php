<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Keda\API\ScaledJob\ScaledJob;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\ScaledJobInterface;
use Klass8s\Event\ManifestGeneratedEventInterface;

readonly class ScaledJobGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.scaled_job';

    public function __construct(
        private ScaledJobInterface $manifest,
        private ScaledJob $apiResource,
        private AppInterface $app
    ) {
    }

    public function manifest(): ScaledJobInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->apiResource;
    }

    public function scaledJob(): ScaledJob
    {
        return $this->apiResource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
