<?php

declare(strict_types=1);

namespace Klass8s\Core;

use K8Schema\APIResourceInterface;

interface SelfRenderingManifestInterface extends ManifestInterface
{
    public function render(): ApiResourceInterface;
}
