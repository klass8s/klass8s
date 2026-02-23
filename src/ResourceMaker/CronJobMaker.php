<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\CronJobSpec;
use K8Schema\Api\Batch\V1\JobTemplateSpec;
use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\LabelSelector;
use Klass8s\Core\CronJob\CronJobInterface;
use Klass8s\Core\Job\JobInterface;
use Klass8s\Core\Job\JobSpecProcessor;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Pod\Policy\RestartPolicy;
use Klass8s\Event\CronJobGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;

final class CronJobMaker extends AbstractResourceMaker
{
    private readonly JobSpecProcessor $jobSpecProcessor;

    public function __construct(JobSpecProcessor $jobSpecProcessor)
    {
        $this->jobSpecProcessor = $jobSpecProcessor;
    }

    protected function makeResource(ManifestInterface|CronJobInterface $manifest): CronJob
    {
        $spec = new CronJobSpec($manifest->schedule());
        $cronJob = new CronJob($spec);

        $app = $manifest->app;
        $app::configureMeta($manifest, $cronJob);

        $this->configureJobTemplate($spec->jobTemplate(), $manifest->job());

        $concurrencyPolicy = $manifest->concurrencyPolicy();
        $failedJobsHistoryLimit = $manifest->failedJobsHistoryLimit();
        $startingDeadlineSeconds = $manifest->startingDeadlineSeconds();
        $successfulJobsHistoryLimit = $manifest->successfulJobsHistoryLimit();
        $spec->setSuspend($manifest->suspendCronJob());
        $spec->setConcurrencyPolicy($concurrencyPolicy->toString());
        if (null !== $failedJobsHistoryLimit) {
            $spec->setFailedJobsHistoryLimit($failedJobsHistoryLimit);
        }
        if (null !== $startingDeadlineSeconds) {
            $spec->setStartingDeadlineSeconds($startingDeadlineSeconds);
        }
        if (null !== $successfulJobsHistoryLimit) {
            $spec->setSuccessfulJobsHistoryLimit($successfulJobsHistoryLimit);
        }

        $timeZone = $manifest->timeZone();
        if (null !== $timeZone) {
            try {
                new \DateTimeZone($timeZone);
                $spec->setTimeZone($timeZone);
            } catch (\Exception) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid timezone "%s" in cron job "%s".', $timeZone, $cronJob->metadata()->getName())
                );
            }
        }

        $manifest->configureCronJob($cronJob);

        if (null === $cronJob->getSpec()->jobTemplate()->spec()->template()->spec()->getRestartPolicy()) {
            $cronJob->getSpec()->jobTemplate()->spec()->template()->spec()->setRestartPolicy(
                RestartPolicy::Never->toString()
            );
        }

        $this->dispatcher->dispatch(new CronJobGeneratedEvent($manifest, $cronJob, $app), CronJobGeneratedEvent::NAME);

        return $cronJob;
    }

    private function configureJobTemplate(JobTemplateSpec $templateSpec, JobInterface $manifest): void
    {
        $jobSpec = $templateSpec->spec();

        // Populate selector to the resource only if manifest actually fills it with labels or expressions
        $jobSpecSelector = $jobSpec->getSelector() ?? new LabelSelector();
        $manifest->selector(new SelectorConfigurator($jobSpecSelector));
        if ($jobSpecSelector->matchExpressions()->count() > 0 || $jobSpecSelector->matchLabels()->count() > 0) {
            $jobSpec->setSelector($jobSpecSelector);
        }

        $this->jobSpecProcessor->process($manifest, $jobSpec);
        foreach (($jobSpec->getSelector()?->matchLabels()->all() ?? []) as $name => $value) {
            $templateSpec->metadata()->labels()->add($name, $value);
        }
    }

    protected function supportedClass(): string
    {
        return CronJobInterface::class;
    }
}
