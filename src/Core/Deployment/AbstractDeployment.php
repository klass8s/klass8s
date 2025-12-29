<?php

declare(strict_types=1);

namespace Klass8s\Core\Deployment;

use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Core\V1\PodSpec;
use K8Schema\Collection\StringMap;
use Klass8s\Core\AbstractManifest;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\ManifestReference;
use Klass8s\Core\Pod\Affinity\AffinityConfigurator;
use Klass8s\Core\Pod\ImagePullSecrets\ImagePullSecretsConfigurator;
use Klass8s\Core\Pod\Policy\RestartPolicy;
use Klass8s\Core\Pod\PriorityClass\PriorityClassConfigurator;
use Klass8s\Core\Pod\Toleration\TolerationsConfigurator;
use Klass8s\Core\Pod\Topology\TopologySpreadConstraintsConfigurator;
use Klass8s\Core\Pod\Volume\VolumesConfigurator;

abstract class AbstractDeployment extends AbstractManifest implements DeploymentInterface
{
    public function affinity(AffinityConfigurator $affinity): void
    {
    }

    public function initContainers(): iterable
    {
        return [];
    }

    public function imagePullSecrets(ImagePullSecretsConfigurator $secrets): void
    {
    }

    public function nodeSelector(StringMap $nodeSelector): void
    {
    }

    public function tolerations(TolerationsConfigurator $tolerations): void
    {
    }

    public function volumes(VolumesConfigurator $volumes): void
    {
    }

    public function restartPolicy(): ?RestartPolicy
    {
        return null;
    }

    public function configurePodSpec(PodSpec $spec): void
    {
    }

    public function replicas(): int
    {
        return 1;
    }

    public function revisionHistoryLimit(): int
    {
        return 1;
    }

    public function selector(SelectorConfigurator $selector): void
    {
    }

    public function strategy(StrategyConfigurator $strategy): void
    {
    }

    public function minReadySeconds(): int|null
    {
        return null;
    }

    public function progressDeadlineSeconds(): int|null
    {
        return null;
    }

    public function priorityClass(PriorityClassConfigurator $priorityClass): void
    {
    }

    public function serviceAccountName(): ?string
    {
        return null;
    }

    public function serviceAccount(): ?ManifestReference
    {
        return null;
    }

    public function terminationGracePeriodSeconds(): ?int
    {
        return null;
    }

    public function topologySpreadConstraints(TopologySpreadConstraintsConfigurator $constraints): void
    {
    }

    public function configureDeployment(Deployment $deployment): void
    {
    }

    final public static function kind(): string
    {
        return Deployment::KIND;
    }

    final public static function apiVersion(): string
    {
        return Deployment::API_VERSION;
    }
}
