<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;

interface ResourceMakerInterface
{
    public function make(ManifestInterface $manifest, AppInterface $app): APIResourceInterface;
    public function supports(ManifestInterface $manifest, AppInterface $app): bool;
}
