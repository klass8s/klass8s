<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Scheduling\V1\PriorityClass;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\PriorityClass\PriorityClassInterface;

class PriorityClassMaker extends AbstractResourceMaker
{
    protected function supportsClass(): string
    {
        return PriorityClassInterface::class;
    }

    protected function makeResource(ManifestInterface|PriorityClassInterface $manifest, AppInterface $app): PriorityClass
    {
        $priorityClass = new PriorityClass($manifest->value());
        $app->metadataHelper()->configureMeta($manifest, $priorityClass);
        if ($description = $manifest->description()) {
            $priorityClass->setDescription($description);
        }
        $priorityClass->setGlobalDefault($manifest->globalDefault());
        if ($preemptionPolicy = $manifest->preemptionPolicy()) {
            $priorityClass->setPreemptionPolicy($preemptionPolicy->toString());
        }

        return $priorityClass;
    }
}
