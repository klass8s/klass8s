<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\ResourceMaker;

use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Keda\API\ScaledJob\ScaledJob;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\Keda\Event\ScaledJobGeneratedEvent;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\RolloutConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator\ScalingStrategyConfigurator;
use Klass8s\Autoscaling\Keda\Manifest\ScaledJob\ScaledJobInterface;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Core\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;
use Klass8s\Util\ClassName;

final class ScaledJobMaker extends AbstractResourceMaker
{
    public function __construct(private readonly TriggerBuildersRegistry $buildersRegistry)
    {
    }

    protected function supportsClass(): string
    {
        return ScaledJobInterface::class;
    }

    protected function makeResource(ManifestInterface|ScaledJobInterface $manifest, AppInterface $app): ScaledJob
    {
        $jobTargetRef = $manifest->jobTargetRef();

        $scaledJob = new ScaledJob($jobTargetRef);
        $app->metadataHelper()->configureMeta($manifest, $scaledJob);

        if ($envSourceContainerName = $manifest->envSourceContainerName()) {
            $scaledJob->setEnvSourceContainerName($envSourceContainerName);
        }

        $scaledJob
            ->setPollingInterval($manifest->pollingInterval())
            ->setSuccessfulJobsHistoryLimit($manifest->successfulJobsHistoryLimit())
            ->setFailedJobsHistoryLimit($manifest->failedJobsHistoryLimit())
            ->setMinReplicaCount($manifest->minReplicaCount())
            ->setMaxReplicaCount($manifest->maxReplicaCount());

        $scalingStrategy = new ScalingStrategyConfigurator($scaledJob->scalingStrategy());
        $manifest->scalingStrategy($scalingStrategy);

        $rollout = new RolloutConfigurator($scaledJob->rollout());
        $manifest->rollout($rollout);

        $triggers = [];
        foreach ($manifest->triggers($this->buildersRegistry) as $trigger) {
            if (!$trigger instanceof Trigger) {
                throw new \TypeError(
                    sprintf(
                        'Each element of %s::triggers() must be an instance of %s, %s given',
                        ClassName::real($manifest),
                        Trigger::class,
                        is_object($trigger) ? $trigger::class : gettype($trigger)
                    )
                );
            }

            $triggers[] = $trigger;
        }

        $scaledJob->setTriggers($triggers);

        $manifest->configureScaledJob($scaledJob);

        $this->dispatcher->dispatch(
            new ScaledJobGeneratedEvent($manifest, $scaledJob, $app),
            ScaledJobGeneratedEvent::NAME
        );

        return $scaledJob;
    }
}
