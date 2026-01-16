<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\APIResourceInterface;
use Klass8s\Manifest\ManifestInterface;

interface ManifestGeneratedEventInterface
{
    public function manifest(): ManifestInterface;

    public function apiResource(): APIResourceInterface;
}
