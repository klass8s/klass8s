<?php

declare(strict_types=1);

namespace Klass8s\Core\Job;

use K8Schema\Api\Batch\V1\Job;
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
use Klass8s\Manifest\AbstractManifest;
use Klass8s\Manifest\Reference\ManifestClassReference;

abstract class AbstractJob extends AbstractManifest implements JobInterface
{
    public function selector(SelectorConfigurator $selector): void
    {
    }

    public function backoffLimit(): ?int
    {
        return null;
    }

    public function activeDeadlineSeconds(): ?int
    {
        return null;
    }

    public function ttlSecondsAfterFinished(): ?int
    {
        return null;
    }

    public function completions(): ?int
    {
        return null;
    }

    public function manualSelector(): ?bool
    {
        return null;
    }

    public function parallelism(): ?int
    {
        return null;
    }

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

    public function priorityClass(PriorityClassConfigurator $priorityClass): void
    {
    }

    public function restartPolicy(): ?RestartPolicy
    {
        return null;
    }

    public function suspend(): bool
    {
        return false;
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

    public function configurePodSpec(PodSpec $spec): void
    {
    }

    public function configureJob(Job $job): void
    {
    }

    public static function kind(): string
    {
        return Job::KIND;
    }

    public static function apiVersion(): string
    {
        return Job::API_VERSION;
    }
}
