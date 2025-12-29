<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Policy;

enum RestartPolicy: string
{
    case Always = 'Always';
    case OnFailure = 'OnFailure';
    case Never = 'Never';

    public function toString(): string
    {
        return $this->value;
    }
}
