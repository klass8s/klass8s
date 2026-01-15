<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Scheduling\V1\PriorityClass;
use Klass8s\Core\PriorityClass\PriorityClassInterface;
use Klass8s\Manifest\ManifestInterface;

final class PriorityClassMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return PriorityClassInterface::class;
    }

    protected function makeResource(ManifestInterface|PriorityClassInterface $manifest): PriorityClass
    {
        $priorityClass = new PriorityClass($manifest->value());
        $app = $manifest->app;
        $app::configureMeta($manifest, $priorityClass);
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
