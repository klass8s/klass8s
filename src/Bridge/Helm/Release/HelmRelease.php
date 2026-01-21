<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

use Klass8s\Bridge\Helm\Chart\ChartReference;
use Klass8s\Bridge\Helm\ValuesProviderInterface;

final readonly class HelmRelease implements HelmReleaseInterface
{
    public function __construct(
        public string $name,
        public ChartReference $chart,
        ?array $values = null,
        private ?ValuesProviderInterface $valuesProvider = null,
    ) {
        if (null !== $values && null !== $valuesProvider) {
            throw new \InvalidArgumentException('Cannot set both $values and $valuesProvider.');
        }

        if (null === $values && null === $valuesProvider) {
            throw new \InvalidArgumentException('Either $values or $valuesProvider must be set.');
        }
    }

    public function values(): array
    {
        return $this->values ?? $this->valuesProvider->values();
    }
}
