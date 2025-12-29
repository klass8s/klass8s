<?php

declare(strict_types=1);

namespace Klass8s\Core\CronJob;

enum ConcurrencyPolicy: string
{
    case Allow = 'Allow';
    case Forbid = 'Forbid';
    case Replace = 'Replace';

    public function toString(): string
    {
        return $this->value;
    }
}
