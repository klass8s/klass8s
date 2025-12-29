<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

enum VolumeMode: string
{
    case Filesystem = 'Filesystem';
    case Block = 'Block';

    public function toString(): string
    {
        return $this->value;
    }
}
