<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\DownwardAPIVolumeFile;
use K8Schema\Api\Core\V1\DownwardAPIVolumeSource;
use K8Schema\Api\Core\V1\Volume;
use Klass8s\Core\Container\Resources\ContainerResourcesField;
use Klass8s\Core\Pod\PodField;

class DownwardAPIVolumeBuilder extends AbstractVolumeBuilder
{
    private DownwardAPIVolumeSource $source;

    public function __construct()
    {
        $this->source = new DownwardAPIVolumeSource();
    }

    public function init(Volume $volume): void
    {
        $volume->setDownwardAPI($this->source);
    }

    public function setDefaultMode(int $mode): self
    {
        $this->source->setDefaultMode($mode);

        return $this;
    }

    public function exposePodInfo(string $filePath, PodField $podField, int $mode = null): self
    {
        $file = new DownwardAPIVolumeFile($filePath);
        $file->setFieldRef($podField->selector());
        if (null !== $mode) {
            $file->setMode($mode);
        }

        $this->source->items()->add($file);

        return $this;
    }

    public function exposeContainerResources(string $filePath, ContainerResourcesField $resourcesField, int $mode = null): self
    {
        $file = new DownwardAPIVolumeFile($filePath);
        $file->setResourceFieldRef($resourcesField->selector());
        if (null !== $mode) {
            $file->setMode($mode);
        }

        $this->source->items()->add($file);

        return $this;
    }
}
