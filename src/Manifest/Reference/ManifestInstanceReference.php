<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyManager;

final readonly class ManifestInstanceReference implements ManifestReferenceInterface
{
    public function __construct(
        public ManifestInterface $manifest,
        public ?string $apiGroup = null,
    ) {
    }

    public function appAlias(): string
    {
        return $this->manifest->app->alias();
    }

    public function apiGroup(): ?string
    {
        return $this->apiGroup;
    }

    public function className(): string
    {
        return ProxyManager::unwrapClassName($this->manifest);
    }
}
