<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Labels;

use Klass8s\Manifest\ManifestInterface;

interface LabelsGeneratorInterface
{
    public function forManifest(ManifestInterface $manifest): array;
}
