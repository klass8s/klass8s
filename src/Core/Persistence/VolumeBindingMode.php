<?php

declare(strict_types=1);

namespace Klass8s\Core\Persistence;

enum VolumeBindingMode: string
{
    case Immediate = 'Immediate';
    case WaitForFirstConsumer = 'WaitForFirstConsumer';

    public function toString(): string
    {
        return $this->value;
    }
}
