<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

final class HelmReleaseRegistry
{
    public private(set) array $releases = [];

    public function add(HelmReleaseInterface $release): void
    {
        if (array_key_exists($release->name, $this->releases)) {
            if ($this->releases[$release->name] === $release) {
                return;
            }

            throw new \LogicException(sprintf('Release "%s" already exists in the registry.', $release->name));
        }

        $this->releases[$release->name] = $release;
    }
}
