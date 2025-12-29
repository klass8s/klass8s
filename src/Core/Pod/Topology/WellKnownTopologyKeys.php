<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Topology;

final readonly class WellKnownTopologyKeys
{
    public const string HOSTNAME = 'kubernetes.io/hostname';
    public const string ZONE = 'topology.kubernetes.io/zone';
    public const string REGION = 'topology.kubernetes.io/region';
}
