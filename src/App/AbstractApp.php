<?php

declare(strict_types=1);

namespace Klass8s\App;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Config\ConfigAwareTrait;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\ManifestNameResolver;
use Klass8s\Manifest\MetadataAwareInterface;
use Klass8s\Manifest\MetadataConfigurator;
use Klass8s\Registry\ManifestRegistry;

abstract class AbstractApp implements AppInterface
{
    use ConfigAwareTrait;

    protected string $alias;

    public function __construct(protected ManifestRegistry $manifestRegistry)
    {
    }

    public function alias(): string
    {
        return $this->alias;
    }

    public function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    public function manifestConfig(ManifestInterface $manifest): array
    {
        return $this->config['manifests'][$manifest->manifestInstanceName] ?? [];
    }

    public function resourceNameByManifestClass(string $manifestClass, ?string $classToValidateAgainst = null): string
    {
        $name = ManifestNameResolver::resourceNameFromManifestClass($manifestClass, $this->alias);
        if (null !== $classToValidateAgainst && !is_a($manifestClass, $classToValidateAgainst, true)) {
            throw new \LogicException(sprintf('Manifest class "%s" is not a subclass of "%s".', $manifestClass, $classToValidateAgainst));
        }
        if (!$this->ownsManifestClass($manifestClass)) {
            throw new \InvalidArgumentException(sprintf('App "%s" does not own manifest class "%s".', $this->alias, $manifestClass));
        }

        return $name;
    }

    public function readFile(string $filePath): string
    {
        $appClass = new \ReflectionObject($this);
        $appDir = dirname($appClass->getFileName());
        $path = $appDir.'/Resources/'.ltrim($filePath, '/');
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(
                sprintf('File "%s" does not exist', $path)
            );
        }

        return file_get_contents($path);
    }

    /**
     * @param class-string<ManifestInterface> $manifestClass
     */
    public function ownsManifestClass(string $manifestClass): bool
    {
        return null !== $this->getManifestByClass($manifestClass);
    }

    /**
     * @template T of ManifestInterface
     *
     * @param class-string<T> $manifestClass
     * @return T|null
     */
    public function getManifestByClass(string $manifestClass): ?ManifestInterface
    {
        return $this->manifestRegistry->query($this->alias)
            ->instancesOf($manifestClass)
            ->instanceName(ManifestNameResolver::manifestNameFromClass($manifestClass))
            ->getFirstResult();
    }

    public static function configureMeta(MetadataAwareInterface $metadataAware, APIResourceInterface $apiObject): ObjectMeta
    {
        $meta = $apiObject->metadata();
        $metaConfigurator = new MetadataConfigurator($meta->labels(), $meta->annotations());
        $metadataAware->metadata($metaConfigurator);

        if ($metadataAware instanceof ManifestInterface) {
            $meta->setName($metadataAware->resourceName);
        }

        return $meta;
    }
}
