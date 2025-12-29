<?php

declare(strict_types=1);

namespace Klass8s\Core\PriorityClass;

enum PreemptionPolicy: string
{
    case Never = 'Never';
    case PreemptLowerPriority = 'PreemptLowerPriority';

    public function toString(): string
    {
        return $this->value;
    }
}
