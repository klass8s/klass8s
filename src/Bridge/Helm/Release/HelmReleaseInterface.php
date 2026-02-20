<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

use Klass8s\Bridge\Helm\Chart\Reference\ChartReference;

interface HelmReleaseInterface
{
    public string $name { get; }
    public ChartReference $chart { get; }
    public int $deployPriority { get; }

    /**
     * @return array<string, mixed>
     */
    public function values(): array;
}
