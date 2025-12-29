<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Toleration;

enum TolerationOperator: string
{
    case Exists = 'Exists';
    case Equal = 'Equal';
}
