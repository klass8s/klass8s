<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledJob;

use K8Schema\Api\Batch\V1\JobSpec;
use Klass8s\Autoscaling\Keda\API\ScaledJob\ScaledJob;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\RolloutConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\ScalingStrategyConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractScaledJob extends AbstractManifest implements ScaledJobInterface
{
    public function __construct(private readonly JobSpec $jobTargetRef)
    {
    }

    public function jobTargetRef(): JobSpec
    {
        return $this->jobTargetRef;
    }

    public function pollingInterval(): int
    {
        return 30;
    }

    public function successfulJobsHistoryLimit(): int
    {
        return 5;
    }

    public function failedJobsHistoryLimit(): int
    {
        return 5;
    }

    public function envSourceContainerName(): ?string
    {
        return null;
    }

    public function minReplicaCount(): int
    {
        return 0;
    }

    public function maxReplicaCount(): int
    {
        return 100;
    }

    public function rollout(RolloutConfigurator $rollout): void
    {
    }

    public function scalingStrategy(ScalingStrategyConfigurator $strategy): void
    {
    }

    public function triggers(TriggerBuildersRegistry $builders): iterable
    {
        return [];
    }

    public function configureScaledJob(ScaledJob $scaledJob): void
    {
    }

    public static function apiVersion(): string
    {
        return ScaledJob::API_VERSION;
    }

    public static function kind(): string
    {
        return ScaledJob::KIND;
    }
}
