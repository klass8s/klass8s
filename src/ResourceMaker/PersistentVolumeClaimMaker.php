<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\PersistentVolumeClaim;
use K8Schema\Api\Core\V1\VolumeResourceRequirements;
use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\LabelSelector;
use K8Schema\APIResourceInterface;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Persistence\PersistentVolumeClaimInterface;
use Klass8s\Core\Persistence\PvcResourcesConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;

final class PersistentVolumeClaimMaker extends AbstractResourceMaker
{
    public function __construct(private readonly ManifestClassReferenceConverter $referencesService)
    {
    }

    protected function supportedClass(): string
    {
        return PersistentVolumeClaimInterface::class;
    }

    protected function makeResource(ManifestInterface|PersistentVolumeClaimInterface $manifest): APIResourceInterface
    {
        $pvc = new PersistentVolumeClaim();
        $spec = $pvc->spec();

        $app = $manifest->app;
        $app::configureMeta($manifest, $pvc);
        $this->configureAccessModes($manifest, $pvc);
        $this->configureDataSource($manifest, $pvc);

        $specResources = $spec->getResources() ?? new VolumeResourceRequirements();
        $resources = new PvcResourcesConfigurator($specResources);
        $manifest->resources($resources);
        if ($specResources->limits()->count() > 0 || $specResources->requests()->count() > 0) {
            $spec->setResources($specResources);
        }

        $specSelector = $spec->getSelector() ?? new LabelSelector();
        $selector = new SelectorConfigurator($specSelector);
        $manifest->selector($selector);
        if ($specSelector->matchLabels()->count() > 0 || $specSelector->matchExpressions()->count() > 0) {
            $spec->setSelector($specSelector);
        }

        $storageClassName = $manifest->storageClassName();
        if (null !== $storageClassName) {
            $spec->setStorageClassName($storageClassName);
        }

        $spec->setVolumeMode($manifest->volumeMode()->toString());

        if ($volumeName = $manifest->volumeName()) {
            $spec->setVolumeName($volumeName);
        }

        return $pvc;
    }

    private function configureAccessModes(PersistentVolumeClaimInterface $manifest, PersistentVolumeClaim $pvc): void
    {
        foreach ($manifest->accessModes() as $mode) {
            $pvc->spec()->accessModes()->add($mode->toString());
        }
    }

    private function configureDataSource(PersistentVolumeClaimInterface $manifest, PersistentVolumeClaim $pvc): void
    {
        $manifestReference = $manifest->dataSource();
        if (null !== $manifestReference) {
            $dataSource = $this->referencesService->toTypedLocalObjectReference($manifestReference);

            $pvc->spec()->setDataSource($dataSource);
        }
    }
}
