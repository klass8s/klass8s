<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Attribute;

/**
 * Declares that the attributed workload manifest is horizontally autoscaled: the build spawns
 * a KEDA ScaledObject next to the workload and removes `spec.replicas` from it, so that the
 * autoscaler alone owns the replica count.
 *
 * The attribute carries literal defaults only. Deployment-time variance (per-environment
 * sizing, disabling) belongs to an {@see \Klass8s\Autoscaling\AutoscalePolicyInterface}
 * implementation, not to the attribute.
 *
 * Must be paired with at least one trigger attribute ({@see ScalingTriggerAttributeInterface}).
 * The attribute is inherited: the nearest class in the hierarchy that declares it wins, and its
 * values replace the parent's entirely (no per-field merging).
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class Autoscale
{
    public function __construct(
        public int $maxReplicas,
        public int $minReplicas = 1,
        public int $pollingIntervalSeconds = 30,
        public int $cooldownSeconds = 300,
    ) {
        if ($this->maxReplicas < 1) {
            throw new \LogicException(sprintf('#[Autoscale] maxReplicas must be at least 1, %d given.', $this->maxReplicas));
        }
        if ($this->minReplicas < 0) {
            throw new \LogicException(sprintf('#[Autoscale] minReplicas must not be negative, %d given.', $this->minReplicas));
        }
        if ($this->minReplicas > $this->maxReplicas) {
            throw new \LogicException(sprintf('#[Autoscale] minReplicas (%d) must not exceed maxReplicas (%d).', $this->minReplicas, $this->maxReplicas));
        }
        if ($this->pollingIntervalSeconds < 1) {
            throw new \LogicException(sprintf('#[Autoscale] pollingIntervalSeconds must be at least 1, %d given.', $this->pollingIntervalSeconds));
        }
        if ($this->cooldownSeconds < 0) {
            throw new \LogicException(sprintf('#[Autoscale] cooldownSeconds must not be negative, %d given.', $this->cooldownSeconds));
        }
    }
}
