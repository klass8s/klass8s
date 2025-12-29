<?php

declare(strict_types=1);

namespace Klass8s\Core\Service;

enum TrafficDistribution: string
{
    case PreferSameZone = 'PreferSameZone';
    case PreferSameNode = 'PreferSameNode';
}
