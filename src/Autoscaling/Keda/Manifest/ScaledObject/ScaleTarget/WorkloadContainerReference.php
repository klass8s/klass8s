<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaleTarget;

use Klass8s\Manifest\Reference\VersionedManifestClassReference;

final readonly class WorkloadContainerReference
{
    public function __construct(
        public VersionedManifestClassReference $manifestReference,
        public string|null $containerName = null
    ) {
    }
}
