<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart;

final readonly class UnpackedChart
{
    public private(set) string $path;

    public function __construct(string $path)
    {
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('The path "%s" is not a valid directory.', $path));
        }

        $this->path = rtrim($path, '/\\');
    }
}
