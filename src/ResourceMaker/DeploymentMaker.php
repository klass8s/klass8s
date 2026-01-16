<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\DeploymentStrategy;
use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Core\Deployment\StrategyConfigurator;
use Klass8s\Core\Pod\PodTemplateSpecProcessor;
use Klass8s\Event\DeploymentGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\Traits\ConfigureSelectorTrait;

final class DeploymentMaker extends AbstractResourceMaker
{
    use ConfigureSelectorTrait;

    public function __construct(private readonly PodTemplateSpecProcessor $specProcessor)
    {
    }

    public function makeResource(ManifestInterface|DeploymentInterface $manifest): Deployment
    {
        $deployment = new Deployment();

        $this->configureSelector($manifest, $deployment->spec()->selector());
        $this->configureStrategy($manifest, $deployment->spec()->strategy());

        $app = $manifest->app;
        $app::configureMeta($manifest, $deployment);
        $this->specProcessor->process($manifest, $deployment->spec()->template(), $app);

        $spec = $deployment->spec();
        foreach ($spec->selector()->matchLabels()->all() as $name => $value) {
            $deployment->metadata()->labels()->add($name, $value);
            $deployment->spec()->template()->metadata()->labels()->add($name, $value);
        }

        $minReadySeconds = $manifest->minReadySeconds();
        $progressDeadlineSeconds = $manifest->progressDeadlineSeconds();
        $spec
            ->setReplicas($manifest->replicas())
            ->setRevisionHistoryLimit($manifest->revisionHistoryLimit());
        if (null !== $minReadySeconds) {
            $spec->setMinReadySeconds($minReadySeconds);
        }
        if (null !== $progressDeadlineSeconds) {
            $spec->setProgressDeadlineSeconds($progressDeadlineSeconds);
        }
        $manifest->configureDeployment($deployment);

        $this->dispatcher->dispatch(new DeploymentGeneratedEvent($manifest, $deployment, $app), DeploymentGeneratedEvent::NAME);

        return $deployment;
    }

    protected function supportedClass(): string
    {
        return DeploymentInterface::class;
    }

    private function configureStrategy(DeploymentInterface|ManifestInterface $manifest, DeploymentStrategy $strategy): void
    {
        $strategyConfigurator = new StrategyConfigurator($strategy);
        $manifest->strategy($strategyConfigurator);
    }
}
