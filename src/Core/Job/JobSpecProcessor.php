<?php

declare(strict_types=1);

namespace Klass8s\Core\Job;

use K8Schema\Api\Batch\V1\JobSpec;
use K8Schema\Api\Batch\V1\PodFailurePolicy;
use Klass8s\App\AppInterface;
use Klass8s\Core\Job\FailurePolicy\PodFailurePolicyConfigurator;
use Klass8s\Core\Pod\PodTemplateSpecProcessor;

final readonly class JobSpecProcessor
{
    private PodTemplateSpecProcessor $podSpecProcessor;

    public function __construct(PodTemplateSpecProcessor $podSpecProcessor)
    {
        $this->podSpecProcessor = $podSpecProcessor;
    }

    public function process(JobInterface $manifest, JobSpec $spec): void
    {
        $app = $manifest->app;
        $this->podSpecProcessor->process($manifest, $spec->template(), $app);

        foreach (($spec->getSelector()?->matchLabels()->all() ?? []) as $name => $value) {
            $spec->template()->metadata()->labels()->add($name, $value);
        }

        $backoffLimit = $manifest->backoffLimit();
        $activeDeadlineSeconds = $manifest->activeDeadlineSeconds();
        $ttlSecondsAfterFinished = $manifest->ttlSecondsAfterFinished();
        $completions = $manifest->completions();
        $manualSelector = $manifest->manualSelector();
        $parallelism = $manifest->parallelism();
        if (null !== $backoffLimit) {
            $spec->setBackoffLimit($backoffLimit);
        }
        if (null !== $activeDeadlineSeconds) {
            $spec->setActiveDeadlineSeconds($activeDeadlineSeconds);
        }
        if (null !== $ttlSecondsAfterFinished) {
            $spec->setTtlSecondsAfterFinished($ttlSecondsAfterFinished);
        }
        if (null !== $completions) {
            $spec->setCompletions($completions);
        }
        if (null !== $manualSelector) {
            $spec->setManualSelector($manualSelector);
        }
        if (null !== $parallelism) {
            $spec->setParallelism($parallelism);
        }

        $spec->setSuspend($manifest->suspend());

        $policy = new PodFailurePolicy();
        $configurator = new PodFailurePolicyConfigurator($policy);
        $manifest->podFailurePolicy($configurator);
        if (!$configurator->isEmpty()) {
            $spec->setPodFailurePolicy($policy);
        }
    }
}
