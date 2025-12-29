<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

use K8Schema\Api\Core\V1\ResourceRequirements;
use K8Schema\Api\Core\V1\VolumeResourceRequirements;
use Klass8s\Core\Container\Resources\ContainerResource;
use Klass8s\Core\Container\Resources\Memory;

readonly class PvcResourcesConfigurator
{
    public function __construct(private VolumeResourceRequirements|ResourceRequirements $resources)
    {
    }

    public function requestStorage(Memory $memory): static
    {
        $this->resources->requests()->add(ContainerResource::STORAGE->value, $memory->toString());

        return $this;
    }

    public function limitStorage(Memory $memory): static
    {
        $this->resources->limits()->add(ContainerResource::STORAGE->value, $memory->toString());

        return $this;
    }
}
