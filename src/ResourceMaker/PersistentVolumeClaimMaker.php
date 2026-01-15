<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\PersistentVolumeClaim;
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

        $resources = new PvcResourcesConfigurator($spec->resources());
        $manifest->resources($resources);

        $selector = new SelectorConfigurator($spec->selector());
        $manifest->selector($selector);

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
        $accessModes = $pvc->spec()->accessModes();
        foreach ($manifest->accessModes() as $mode) {
            $accessModes->add($mode->toString());
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
