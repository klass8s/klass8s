<?php

declare(strict_types=1);

namespace Klass8s\Core\PriorityClass;

use K8Schema\Api\Scheduling\V1\PriorityClass;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractPriorityClass extends AbstractManifest implements PriorityClassInterface
{
    final public static function kind(): string
    {
        return PriorityClass::KIND;
    }

    public static function apiVersion(): string
    {
        return PriorityClass::API_VERSION;
    }

    public function description(): ?string
    {
        return null;
    }

    public function globalDefault(): bool
    {
        return false;
    }

    public function preemptionPolicy(): ?PreemptionPolicy
    {
        return null;
    }
}
