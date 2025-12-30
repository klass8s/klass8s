<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\ResourceMaker;

use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\ScaledObject;
use Klass8s\Autoscaling\Keda\API\ScaledObject\ScaleTargetReference;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\Keda\Event\ScaledObjectGeneratedEvent;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaledObjectInterface;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaleTarget\WorkloadContainerReference;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Core\Autoscaling\Configurator\BehaviorConfigurator;
use Klass8s\Core\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;
use Klass8s\Util\ClassName;
use Klass8s\Util\VersionedManifestReferencesService;

class ScaledObjectMaker extends AbstractResourceMaker
{
    public function __construct(
        private readonly VersionedManifestReferencesService $referencesService,
        private readonly TriggerBuildersRegistry $buildersRegistry
    ) {
    }

    protected function supportsClass(): string
    {
        return ScaledObjectInterface::class;
    }

    protected function makeResource(ManifestInterface|ScaledObjectInterface $manifest, AppInterface $app): ScaledObject
    {
        $scaledObject = new ScaledObject();
        $app->metadataHelper()->configureMeta($manifest, $scaledObject);

        $targetRef = $this->scaleTargetRef($manifest);
        $scaledObject->setScaleTargetRef($targetRef);
        $scaledObject
            ->setPollingInterval($manifest->pollingInterval())
            ->setCooldownPeriod($manifest->cooldownPeriod())
            ->setMinReplicaCount($manifest->minReplicaCount())
            ->setMaxReplicaCount($manifest->maxReplicaCount());

        if ($idleReplicaCount = $manifest->idleReplicaCount()) {
            $scaledObject->setIdleReplicaCount($idleReplicaCount);
        }

        if ($fallback = $manifest->fallback()) {
            $scaledObject->setFallback($fallback);
        }

        $advanced = $scaledObject->advanced();
        if (($restoreToOriginalReplicaCount = $manifest->restoreToOriginalReplicaCount()) !== null) {
            $advanced->setRestoreToOriginalReplicaCount($restoreToOriginalReplicaCount);
        }
        $hpaConfig = $advanced->horizontalPodAutoscalerConfig();
        if ($hpaName = $manifest->hpaName()) {
            $hpaConfig->setName($hpaName);
        }
        $behaviorConfigurator = new BehaviorConfigurator($advanced->horizontalPodAutoscalerConfig()->behavior());
        $manifest->behavior($behaviorConfigurator);

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

        $scaledObject->setTriggers($triggers);

        $manifest->configureScaledObject($scaledObject);

        $this->dispatcher->dispatch(new ScaledObjectGeneratedEvent($manifest, $scaledObject, $app), ScaledObjectGeneratedEvent::NAME);

        return $scaledObject;
    }

    private function scaleTargetRef(ScaledObjectInterface $manifest): ScaleTargetReference
    {
        $scaleTargetRef = $manifest->scaleTargetRef();
        if ($scaleTargetRef instanceof WorkloadContainerReference) {
            $objectRef = $this->referencesService->toCrossVersionObjectReference($scaleTargetRef->manifestReference);
            $scaleTargetRef = new ScaleTargetReference(
                name: $objectRef->getName(),
                apiVersion: $objectRef->getApiVersion(),
                kind: $objectRef->getKind(),
                envSourceContainerName: $scaleTargetRef->containerName
            );
        }

        return $scaleTargetRef;
    }
}
