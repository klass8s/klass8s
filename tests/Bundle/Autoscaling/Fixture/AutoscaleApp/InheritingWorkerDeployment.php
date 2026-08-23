<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture\AutoscaleApp;

use Klass8s\Autoscaling\Attribute\Autoscale;

/**
 * Redeclares #[Autoscale] (nearest class wins, whole attribute replaced) but no trigger
 * attributes — the parent's trigger set is inherited.
 */
#[Autoscale(maxReplicas: 3)]
class InheritingWorkerDeployment extends ScaledWorkerDeployment
{
    public static function shortName(): string
    {
        return 'worker-two';
    }
}
