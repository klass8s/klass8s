<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Storage\V1\StorageClass;
use Klass8s\Core\Persistence\AllowedTopologiesConfigurator;
use Klass8s\Core\Persistence\StorageClassInterface;
use Klass8s\Manifest\ManifestInterface;

final class StorageClassMaker extends AbstractResourceMaker
{
    public const string DEFAULT_CLASS_ANNOTATION = 'storageclass.kubernetes.io/is-default-class';

    protected function supportedClass(): string
    {
        return StorageClassInterface::class;
    }

    protected function makeResource(ManifestInterface|StorageClassInterface $manifest): StorageClass
    {
        $storageClass = new StorageClass($manifest->provisioner());
        $app = $manifest->app;
        $meta = $app::configureMeta($manifest, $storageClass);
        if ($manifest->isDefaultClass()) {
            $meta->annotations()->add(self::DEFAULT_CLASS_ANNOTATION, 'true');
        }

        $manifest->parameters($storageClass->parameters());
        $storageClass->setReclaimPolicy($manifest->reclaimPolicy()->toString());
        $storageClass->setVolumeBindingMode($manifest->volumeBindingMode()->toString());
        $storageClass->setAllowVolumeExpansion($manifest->allowVolumeExpansion());
        foreach ($manifest->mountOptions() as $mountOption) {
            $storageClass->mountOptions()->add($mountOption);
        }

        $manifest->allowedTopologies(new AllowedTopologiesConfigurator($storageClass->allowedTopologies()));

        return $storageClass;
    }
}
