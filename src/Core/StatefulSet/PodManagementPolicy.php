<?php

declare(strict_types=1);

namespace Klass8s\Core\StatefulSet;

enum PodManagementPolicy: string
{
    case OrderedReady = 'OrderedReady';
    case Parallel = 'Parallel';

    public function toString(): string
    {
        return $this->value;
    }
}
