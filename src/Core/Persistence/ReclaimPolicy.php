<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

enum ReclaimPolicy: string
{
    case Delete = 'Delete';
    case Retain = 'Retain';

    public function toString(): string
    {
        return $this->value;
    }
}
