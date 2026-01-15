<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

final readonly class ManifestClassReference
{
    public function __construct(public string $appAlias, public string $className, public ?string $apiGroup = null)
    {
    }

    public function appAlias(): string
    {
        return $this->appAlias;
    }

    public function apiGroup(): ?string
    {
        return $this->apiGroup;
    }

    public function className(): string
    {
        return $this->className;
    }
}
