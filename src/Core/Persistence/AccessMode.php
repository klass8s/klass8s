<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

enum AccessMode: string
{
    case ReadWriteOnce = 'ReadWriteOnce';
    case ReadOnlyMany = 'ReadOnlyMany';
    case ReadWriteMany = 'ReadWriteMany';

    public function toString(): string
    {
        return $this->value;
    }
}
