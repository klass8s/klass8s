<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject;

use Klass8s\Autoscaling\Attribute\Autoscale;
use Klass8s\Autoscaling\AutoscaleDecision;
use Klass8s\Autoscaling\Keda\API\ScaledObject\ScaleTargetReference;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Manifest\ManifestTemplateInterface;
use Klass8s\Manifest\ManifestTemplateTrait;

/**
 * The ScaledObject spawned for a workload carrying the {@see Autoscale} attribute — one
 * dynamically-named instance per attributed workload, named `{workload short name}-scaler`.
 * Everything it renders was decided before construction: the attribute supplies the timing
 * knobs, the {@see AutoscaleDecision} the replica bounds, and the triggers arrive already
 * resolved from the workload's trigger attributes.
 */
class AutoscaleScaledObject extends AbstractScaledObject implements ManifestTemplateInterface
{
    use ManifestTemplateTrait;

    /**
     * @param Trigger[] $triggers
     */
    public function __construct(
        string $shortName,
        private readonly ScaleTargetReference $scaleTargetRef,
        private readonly Autoscale $attribute,
        private readonly AutoscaleDecision $decision,
        private readonly array $triggers,
    ) {
        $this->dynamicShortName = $shortName;
    }

    public function scaleTargetRef(): ScaleTargetReference
    {
        return $this->scaleTargetRef;
    }

    public function pollingInterval(): int
    {
        return $this->attribute->pollingIntervalSeconds;
    }

    public function cooldownPeriod(): int
    {
        return $this->attribute->cooldownSeconds;
    }

    public function minReplicaCount(): int
    {
        return $this->decision->minReplicas;
    }

    public function maxReplicaCount(): int
    {
        return $this->decision->maxReplicas;
    }

    public function triggers(TriggerBuildersRegistry $builders): iterable
    {
        return $this->triggers;
    }
}
