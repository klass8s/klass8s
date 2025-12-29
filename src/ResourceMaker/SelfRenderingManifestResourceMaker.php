<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\SelfRenderingManifestInterface;

class SelfRenderingManifestResourceMaker extends AbstractResourceMaker
{
    protected function supportsClass(): string
    {
        return SelfRenderingManifestInterface::class;
    }

    protected function makeResource(ManifestInterface|SelfRenderingManifestInterface $manifest, AppInterface $app): APIResourceInterface
    {
        $apiResource = $manifest->render();
        $app->metadataHelper()->configureMeta($manifest, $apiResource);

        return $apiResource;
    }
}
