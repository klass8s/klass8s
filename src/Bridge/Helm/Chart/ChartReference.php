<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart;

final readonly class ChartReference
{
    public function __construct(public string $value, public ?string $version = null)
    {
    }
}
