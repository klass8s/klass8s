<?php

declare(strict_types=1);

namespace Klass8s\Output;

final readonly class RenderedFile
{
    public function __construct(public string $filename, public string $contents)
    {
    }
}
