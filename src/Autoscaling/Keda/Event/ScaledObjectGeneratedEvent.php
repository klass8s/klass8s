<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Event;

use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\ScaledObject;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaledObjectInterface;
use Klass8s\Event\ManifestGeneratedEventInterface;

final readonly class ScaledObjectGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.scaled_object';

    public function __construct(
        private ScaledObjectInterface $manifest,
        private ScaledObject $apiResource,
        private AppInterface $app,
    ) {
    }

    public function manifest(): ScaledObjectInterface
    {
        return $this->manifest;
    }

    public function apiResource(): ScaledObject
    {
        return $this->apiResource;
    }

    public function scaledObject(): ScaledObject
    {
        return $this->apiResource;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
