<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

use K8Schema\Api\Storage\V1\StorageClass;
use K8Schema\Collection\StringMap;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractStorageClass extends AbstractManifest implements StorageClassInterface
{
    final public static function kind(): string
    {
        return StorageClass::KIND;
    }

    public static function apiVersion(): string
    {
        return StorageClass::API_VERSION;
    }

    public function parameters(StringMap $parameters): void
    {
    }

    public function reclaimPolicy(): ReclaimPolicy
    {
        return ReclaimPolicy::Delete;
    }

    public function volumeBindingMode(): VolumeBindingMode
    {
        return VolumeBindingMode::Immediate;
    }

    public function allowVolumeExpansion(): bool
    {
        return false;
    }

    public function mountOptions(): iterable
    {
        return [];
    }

    public function allowedTopologies(AllowedTopologiesConfigurator $topologies): void
    {
    }

    public function isDefaultClass(): bool
    {
        return false;
    }
}
