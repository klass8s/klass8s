<?php

declare(strict_types=1);

namespace Klass8s\Registry;

use Klass8s\Core\FullNameAwareInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Proxy\ProxyFactory;
use Klass8s\Registry\Query\ManifestsQuery;
use Klass8s\Runtime\ManifestRuntimeStatusResolverInterface;
use Klass8s\Util\ManifestShortName;

final class ManifestRegistry
{
    /**
     * @var ManifestInterface[][]
     */
    private array $manifests = [];

    public function __construct(
        private readonly ProxyFactory $proxyFactory,
        private readonly ManifestRuntimeStatusResolverInterface $statusResolver
    ) {
    }

    /**
     * @param string            $appAlias
     * @param ManifestInterface $manifest
     *
     * @throws \ReflectionException
     */
    public function add(string $appAlias, ManifestInterface $manifest): void
    {
        $this->manifests[$appAlias] ??= [];

        $shortName = $manifest instanceof FullNameAwareInterface
            ? $manifest->fullName()
            : ManifestShortName::getFrom($manifest);
        $key = sprintf('%s_%s', $shortName, $manifest::kind());
        if (array_key_exists($key, $this->manifests[$appAlias])) {
            $existingManifestClass = new \ReflectionClass($this->manifests[$appAlias][$key]);
            if ($existingManifestClass->implementsInterface(ProxyInterface::class)) {
                $existingManifestClass = $existingManifestClass->getParentClass();
            }
            throw new \LogicException(
                sprintf(
                    'Manifests instances of classes "%s" and "%s" have the same kind and short name, which is forbidden.',
                    $existingManifestClass->getName(),
                    $manifest::class
                )
            );
        }

        if (!$this->statusResolver->isClassEnabled(new \ReflectionClass($manifest))) {
            return;
        }

        $this->manifests[$appAlias][] = $this->proxyFactory->makeProxy($manifest);
    }

    /**
     * @param string $appAlias
     *
     * @return ManifestInterface[]
     */
    public function forApp(string $appAlias): iterable
    {
        return $this->query($appAlias)->execute();
    }

    public function query(string $appAlias): ManifestsQuery
    {
        $this->manifests[$appAlias] ??= [];
        $manifests = $this->manifests[$appAlias];

        return new ManifestsQuery($manifests);
    }
}
