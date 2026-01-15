<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

use K8Schema\Api\Core\V1\PersistentVolume;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Manifest\AbstractManifest;
use Klass8s\Manifest\Reference\ManifestClassReference;

abstract class AbstractPersistentVolumeClaim extends AbstractManifest implements PersistentVolumeClaimInterface
{
    public function dataSource(): ?ManifestClassReference
    {
        return null;
    }

    public function resources(PvcResourcesConfigurator $resources): void
    {
    }

    public function selector(SelectorConfigurator $selector): void
    {
    }

    public function storageClassName(): ?string
    {
        return null;
    }

    public function volumeMode(): VolumeMode
    {
        return VolumeMode::Filesystem;
    }

    public function volumeName(): ?string
    {
        return null;
    }

    public static function kind(): string
    {
        return PersistentVolume::KIND;
    }

    public static function apiVersion(): string
    {
        return PersistentVolume::API_VERSION;
    }
}
