<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

use Klass8s\Runtime\ManifestRuntimeStatusResolverInterface;

final class HelmReleaseRegistry
{
    /**
     * @var array<string, HelmReleaseInterface>
     */
    private(set) array $releases = [];

    /**
     * @var string[]
     */
    public array $releaseNames {
        get => array_keys($this->releases);
    }

    public function __construct(private readonly ManifestRuntimeStatusResolverInterface $statusResolver)
    {
    }

    public function add(HelmReleaseInterface $release): void
    {
        if (array_key_exists($release->name, $this->releases)) {
            if ($this->releases[$release->name] === $release) {
                return;
            }

            throw new \LogicException(sprintf('Release "%s" already exists in the registry.', $release->name));
        }

        if (!$release->enabled || !$this->statusResolver->isClassEnabled(new \ReflectionClass($release))) {
            return;
        }

        $this->releases[$release->name] = $release;
    }

    public function get(string $name): HelmReleaseInterface
    {
        if (!$this->has($name)) {
            throw new \OutOfBoundsException(sprintf('Release "%s" does not exist in the registry.', $name));
        }

        return $this->releases[$name];
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->releases);
    }
}
