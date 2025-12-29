<?php

declare(strict_types=1);

namespace Klass8s\Core;

interface LabelsGeneratorInterface
{
    public function byManifestClass(string $manifestClass, string|null $appAlias = null): array;
    public function byManifestInstance(ManifestInterface $manifest): array;
}
