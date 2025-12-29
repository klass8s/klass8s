<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling;

enum SelectPolicy: string
{
    case Min = 'Min';
    case Max = 'Max';
    case Disabled = 'Disabled';
}
