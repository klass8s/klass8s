<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\HostPathVolumeSource;
use K8Schema\Api\Core\V1\Volume;
use Klass8s\Core\Pod\Volume\HostPathVolumeType;

class HostPathVolumeBuilder extends AbstractVolumeBuilder
{
    private HostPathVolumeSource $source;

    public function __construct(string $path)
    {
        $this->source = new HostPathVolumeSource($path);
    }

    public function init(Volume $volume): void
    {
        $volume->setHostPath($this->source);
    }

    public function setType(HostPathVolumeType $type): self
    {
        $this->source->setType($type->value);

        return $this;
    }
}
