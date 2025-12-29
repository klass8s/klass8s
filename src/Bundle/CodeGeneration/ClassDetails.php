<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration;

final readonly class ClassDetails
{
    public function __construct(
        public string $className,
        public string $namespace,
        public string $directory,
        public string $fileName,
    ) {
    }

    public function fullFilePath(): string
    {
        return $this->directory.DIRECTORY_SEPARATOR.$this->fileName;
    }
}
