<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\ManifestReference;

interface PersistentVolumeClaimInterface extends ManifestInterface
{
    /**
     * @return AccessMode[]
     */
    public function accessModes(): iterable;
    public function dataSource(): ManifestReference|null;
    public function resources(PvcResourcesConfigurator $resources): void;
    public function selector(SelectorConfigurator $selector): void;
    public function storageClassName(): string|null;
    public function volumeMode(): VolumeMode;
    public function volumeName(): string|null;
}
