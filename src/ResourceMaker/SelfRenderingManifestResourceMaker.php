<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\SelfMakingManifestInterface;

class SelfRenderingManifestResourceMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return SelfMakingManifestInterface::class;
    }

    protected function makeResource(ManifestInterface|SelfMakingManifestInterface $manifest): APIResourceInterface
    {
        $apiResource = $manifest->make();
        $app = $manifest->app;
        $app::configureMeta($manifest, $apiResource);

        return $apiResource;
    }
}
