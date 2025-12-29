<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Bundle\CodeGeneration\ClassDetails;

interface ContextInterface
{
    public function createDedicatedDir(): bool;

    public function kind(): string;

    public function parentInterface(): string;

    public function templateName(): string;

    public function templateVariables(ClassDetails $details, string $manifestName): array;
}
