<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinition;
use Klass8s\App\AppInterface;
use Klass8s\Core\Crd\CustomResourceDefinitionInterface;
use Klass8s\Manifest\ManifestInterface;

final readonly class CustomResourceDefinitionGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.custom_resource_definition';

    public function __construct(
        private CustomResourceDefinitionInterface $manifest,
        private CustomResourceDefinition $crd,
        private AppInterface $app,
    ) {
    }

    public function manifest(): CustomResourceDefinitionInterface|ManifestInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->crd;
    }

    public function crd(): CustomResourceDefinition
    {
        return $this->crd;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
