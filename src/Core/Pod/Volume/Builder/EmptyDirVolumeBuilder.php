<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\EmptyDirVolumeSource;
use K8Schema\Api\Core\V1\Volume;
use Klass8s\Core\Container\Resources\Memory;

class EmptyDirVolumeBuilder extends AbstractVolumeBuilder
{
    private const string MEDIUM_MEMORY = 'Memory';

    private EmptyDirVolumeSource $source;

    public function __construct()
    {
        $this->source = new EmptyDirVolumeSource();
    }

    public function init(Volume $volume): void
    {
        $volume->setEmptyDir($this->source);
    }

    public function useRAM(): self
    {
        $this->source->setMedium(self::MEDIUM_MEMORY);

        return $this;
    }

    public function setSizeLimit(Memory $memory): self
    {
        $this->source->setSizeLimit($memory->toString());

        return $this;
    }
}
