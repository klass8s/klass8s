<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Manifest\ManifestInterface;

class ManifestGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated';

    /**
     * @var bool If true, will prevent generated manifest from being printed / dumped and will exclude it from final manifests generation result
     */
    public bool $preventProcessing = false;

    public function __construct(
        private readonly ManifestInterface $manifest,
        private readonly APIResourceInterface $resource,
    ) {
    }

    public function manifest(): ManifestInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->resource;
    }
}
