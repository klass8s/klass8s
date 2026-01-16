<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use K8Schema\APIResourceInterface;

interface SelfMakingManifestInterface extends ManifestInterface
{
    public function make(): ApiResourceInterface;
}
