<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\Volume;

interface VolumeBuilderInterface
{
    /**
     * @internal
     */
    public function init(Volume $volume): void;
}
