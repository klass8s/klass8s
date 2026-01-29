<?php

declare(strict_types=1);

namespace Klass8s\Registry;

use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyFactory;
use Klass8s\Registry\Query\ManifestsQuery;
use Klass8s\Runtime\ManifestRuntimeStatusResolverInterface;
use Proximity\ProxyInterface;

final class ManifestRegistry
{
    /**
     * @var array<string, array<string, ManifestInterface&ProxyInterface>>
     */
    private array $manifests = [];

    public function __construct(
        private readonly ProxyFactory $proxyFactory,
        private readonly ManifestRuntimeStatusResolverInterface $statusResolver
    ) {
    }

    public function add(ManifestInterface $manifest): void
    {
        $appAlias = $manifest->app->alias();
        $this->manifests[$appAlias] ??= [];

        $key = sprintf('%s_%s', $manifest::kind(), $manifest->computedShortName);
        if (array_key_exists($key, $this->manifests[$appAlias])) {
            $existingManifest = $this->manifests[$appAlias][$key];
            if ($existingManifest === $manifest) {
                return;
            }

            throw new \LogicException(sprintf('Manifests instances of classes "%s" and "%s" have the same kind and instance name, which is forbidden.', $existingManifest::className(), $manifest::class));
        }

        if (!$this->statusResolver->isClassEnabled(new \ReflectionClass($manifest))) {
            return;
        }

        $this->manifests[$appAlias][$key] = $this->proxyFactory->makeProxy($manifest);
    }

    public function query(string $appAlias): ManifestsQuery
    {
        $this->manifests[$appAlias] ??= [];
        $manifests = $this->manifests[$appAlias];

        return new ManifestsQuery($manifests);
    }
}
