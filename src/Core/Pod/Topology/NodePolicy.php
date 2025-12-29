<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Topology;

enum NodePolicy: string
{
    case Honor = 'Honor';
    case Ignore = 'Ignore';
}
