<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling\Metric;

enum TargetType: string
{
    case Utilization = 'Utilization';
    case Value = 'Value';
    case AverageValue = 'AverageValue';
}
