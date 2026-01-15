<?php

declare(strict_types=1);

namespace Klass8s\Core\StatefulSet;

use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Core\V1\PodSpec;
use K8Schema\Collection\StringMap;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Pod\Affinity\AffinityConfigurator;
use Klass8s\Core\Pod\ImagePullSecrets\ImagePullSecretsConfigurator;
use Klass8s\Core\Pod\Policy\RestartPolicy;
use Klass8s\Core\Pod\PriorityClass\PriorityClassConfigurator;
use Klass8s\Core\Pod\Toleration\TolerationsConfigurator;
use Klass8s\Core\Pod\Topology\TopologySpreadConstraintsConfigurator;
use Klass8s\Core\Pod\Volume\VolumesConfigurator;
use Klass8s\Core\StatefulSet\UpdateStrategy\UpdateStrategyConfigurator;
use Klass8s\Manifest\AbstractManifest;
use Klass8s\Manifest\Reference\ManifestClassReference;

abstract class AbstractStatefulSet extends AbstractManifest implements StatefulSetInterface
{
    public static function kind(): string
    {
        return StatefulSet::KIND;
    }

    public static function apiVersion(): string
    {
        return StatefulSet::API_VERSION;
    }

    public function initContainers(): iterable
    {
        return [];
    }

    public function affinity(AffinityConfigurator $affinity): void
    {
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

    public function priorityClass(PriorityClassConfigurator $priorityClass): void
    {
    }

    public function podManagementPolicy(): ?PodManagementPolicy
    {
        return null;
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

    public function updateStrategy(UpdateStrategyConfigurator $updateStrategy): void
    {
    }

    public function volumeClaimTemplates(): iterable
    {
        return [];
    }

    public function serviceAccountName(): ?string
    {
        return null;
    }

    public function serviceAccount(): ?ManifestClassReference
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

    public function configureStatefulSet(StatefulSet $statefulSet): void
    {
    }
}
