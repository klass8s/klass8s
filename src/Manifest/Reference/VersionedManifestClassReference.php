<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

final readonly class VersionedManifestClassReference
{
    public function __construct(public string $appAlias, public string $className, public ?string $apiVersion = null)
    {
    }
}
