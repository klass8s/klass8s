<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Checksum;

class ChecksumAnnotation
{
    public function __construct(private string $name, private string $value)
    {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function value(): string
    {
        return $this->value;
    }
}
