<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod;

use K8Schema\Api\Core\V1\PodSpec;
use K8Schema\Collection\StringMap;
use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\ManifestReference;
use Klass8s\Core\Pod\Affinity\AffinityConfigurator;
use Klass8s\Core\Pod\ImagePullSecrets\ImagePullSecretsConfigurator;
use Klass8s\Core\Pod\Policy\RestartPolicy;
use Klass8s\Core\Pod\PriorityClass\PriorityClassConfigurator;
use Klass8s\Core\Pod\Toleration\TolerationsConfigurator;
use Klass8s\Core\Pod\Topology\TopologySpreadConstraintsConfigurator;
use Klass8s\Core\Pod\Volume\VolumesConfigurator;

interface PodSpecInterface
{
    /**
     * @return ContainerInterface[]
     */
    public function containers(): iterable;

    /**
     * @return ContainerInterface[]
     */
    public function initContainers(): iterable;

    public function affinity(AffinityConfigurator $affinity): void;
    public function imagePullSecrets(ImagePullSecretsConfigurator $secrets): void;
    public function nodeSelector(StringMap $nodeSelector): void;
    public function tolerations(TolerationsConfigurator $tolerations): void;
    public function volumes(VolumesConfigurator $volumes): void;
    public function restartPolicy(): ?RestartPolicy;
    public function configurePodSpec(PodSpec $spec): void;
    public function priorityClass(PriorityClassConfigurator $priorityClass): void;
    public function serviceAccountName(): ?string;
    public function serviceAccount(): ?ManifestReference;
    public function terminationGracePeriodSeconds(): ?int;
    public function topologySpreadConstraints(TopologySpreadConstraintsConfigurator $constraints): void;
}
