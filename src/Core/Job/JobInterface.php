<?php

declare(strict_types=1);

namespace Klass8s\Core\Job;

use K8Schema\Api\Batch\V1\Job;
use Klass8s\Core\Job\FailurePolicy\PodFailurePolicyConfigurator;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Pod\PodTemplateSpecInterface;
use Klass8s\Manifest\ManifestInterface;

interface JobInterface extends PodTemplateSpecInterface, ManifestInterface
{
    public function selector(SelectorConfigurator $selector): void;
    public function backoffLimit(): ?int;
    public function activeDeadlineSeconds(): ?int;
    public function ttlSecondsAfterFinished(): ?int;
    public function completions(): ?int;
    public function manualSelector(): ?bool;
    public function parallelism(): ?int;
    public function suspend(): bool;

    /**
     * The Job's `podFailurePolicy`, rule by rule; nothing added = no policy rendered.
     * Needs `restartPolicy: Never` on the pod template.
     */
    public function podFailurePolicy(PodFailurePolicyConfigurator $policy): void;
    public function configureJob(Job  $job): void;
}
