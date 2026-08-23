<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

/**
 * Marks a workload manifest as one that must never be scaled horizontally — e.g. a singleton
 * consumer that races on shared state when more than one replica runs. Combining this marker
 * with the {@see Attribute\Autoscale} attribute fails the build.
 */
interface NonScalableWorkloadInterface
{
}
