<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Manifest\ManifestInterface;

interface ResourceMakerInterface
{
    public function make(ManifestInterface $manifest): APIResourceInterface;
    public function supports(ManifestInterface $manifest): bool;
}
