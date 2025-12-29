<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Toleration;

enum TolerationEffect: string
{
    case NoSchedule = 'NoSchedule';
    case PreferNoSchedule = 'PreferNoSchedule';
    case NoExecute = 'NoExecute';
}
