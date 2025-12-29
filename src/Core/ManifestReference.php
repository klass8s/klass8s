<?php

declare(strict_types=1);

namespace Klass8s\Core;

final readonly class ManifestReference
{
    public function __construct(public string $appAlias, public string $className, public string|null $apiGroup = null)
    {
    }

    public function appAlias(): string
    {
        return $this->appAlias;
    }

    public function apiGroup(): string|null
    {
        return $this->apiGroup;
    }

    public function className(): string
    {
        return $this->className;
    }
}
