<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\PersistentVolumeClaimVolumeSource;
use K8Schema\Api\Core\V1\Volume;

class PVCVolumeBuilder extends AbstractVolumeBuilder
{
    private PersistentVolumeClaimVolumeSource $source;

    public function __construct(private readonly string $pvcName)
    {
        $this->source = new PersistentVolumeClaimVolumeSource($this->pvcName);
    }

    public function init(Volume $volume): void
    {
        $volume->setPersistentVolumeClaim($this->source);
    }

    public function setReadOnly(): void
    {
        $this->source->setReadOnly(true);
    }
}
