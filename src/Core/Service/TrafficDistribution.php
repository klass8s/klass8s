<?php

declare(strict_types=1);

namespace Klass8s\Core\Service;

enum TrafficDistribution: string
{
    case PreferClose = 'PreferClose';
    case PreferSameZone = 'PreferSameZone';
    case PreferSameNode = 'PreferSameNode';
}
