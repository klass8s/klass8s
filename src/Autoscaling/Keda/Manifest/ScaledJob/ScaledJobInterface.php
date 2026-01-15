<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledJob;

use K8Schema\Api\Batch\V1\JobSpec;
use Klass8s\Autoscaling\Keda\API\ScaledJob\ScaledJob;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\RolloutConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\ScalingStrategyConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Manifest\ManifestInterface;

interface ScaledJobInterface extends ManifestInterface
{
    public function jobTargetRef(): JobSpec;
    public function pollingInterval(): int;
    public function successfulJobsHistoryLimit(): int;
    public function failedJobsHistoryLimit(): int;
    public function envSourceContainerName(): ?string;
    public function minReplicaCount(): int;
    public function maxReplicaCount(): int;
    public function rollout(RolloutConfigurator $rollout): void;
    public function scalingStrategy(ScalingStrategyConfigurator $strategy): void;

    /**
     * @return Trigger[]
     */
    public function triggers(TriggerBuildersRegistry $builders): iterable;

    public function configureScaledJob(ScaledJob $scaledJob): void;
}
