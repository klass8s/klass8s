<?php

declare(strict_types=1);

namespace Klass8s\Runtime;

interface ManifestStatusResolverStrategyInterface
{
    public function isClassEnabled(\ReflectionClass $class): bool;
}
