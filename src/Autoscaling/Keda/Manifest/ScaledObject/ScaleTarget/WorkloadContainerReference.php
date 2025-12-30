<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaleTarget;

use Klass8s\Core\VersionedManifestReference;

final readonly class WorkloadContainerReference
{
    public function __construct(
        public VersionedManifestReference $manifestReference,
        public string|null $containerName = null
    ) {
    }
}
