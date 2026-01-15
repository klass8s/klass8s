<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

interface ManifestReferenceInterface
{
    public function appAlias(): string;
    public function apiGroup(): ?string;
    public function className(): string;
}
