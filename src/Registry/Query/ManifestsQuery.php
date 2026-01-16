<?php

declare(strict_types=1);

namespace Klass8s\Registry\Query;

use Klass8s\Manifest\FullNameAwareInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\ManifestNameResolver;

final class ManifestsQuery
{
    /**
     * @var \Closure[]
     */
    private array $closures = [];

    /**
     * @param ManifestInterface[] $manifests
     */
    public function __construct(private readonly array $manifests)
    {
    }

    public function instancesOf(string $className): self
    {
        return $this->addClosure(
            fn (ManifestInterface $manifest): bool => $manifest instanceof $className
        );
    }

    public function instanceName(string $name): self
    {
        return $this->addClosure(fn (ManifestInterface $manifest): bool => !$manifest instanceof FullNameAwareInterface && $name === $manifest->manifestInstanceName);
    }

    /**
     * @return ManifestInterface[]
     */
    public function execute(): iterable
    {
        foreach ($this->manifests as $manifest) {
            foreach ($this->closures as $closure) {
                if (!$closure($manifest)) {
                    continue 2;
                }
            }

            yield $manifest;
        }
    }

    public function getFirstResult(): ?ManifestInterface
    {
        foreach ($this->execute() as $manifest) {
            return $manifest;
        }

        return null;
    }

    private function addClosure(\Closure $closure): self
    {
        $this->closures[] = $closure;

        return $this;
    }
}
