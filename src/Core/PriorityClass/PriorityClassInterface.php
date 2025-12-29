<?php

declare(strict_types=1);

namespace Klass8s\Core\PriorityClass;

use Klass8s\Core\ManifestInterface;

interface PriorityClassInterface extends ManifestInterface
{
    public function description(): ?string;
    public function globalDefault(): bool;
    public function preemptionPolicy(): ?PreemptionPolicy;
    public function value(): int;
}
