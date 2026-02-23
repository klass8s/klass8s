<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Batch\V1\Job;
use Klass8s\Core\CronJob\CronJobInterface;
use Klass8s\Core\Job\JobInterface;
use Klass8s\Core\Job\JobSpecProcessor;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Pod\Policy\RestartPolicy;
use Klass8s\Event\JobGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;

final class JobMaker extends AbstractResourceMaker
{
    private JobSpecProcessor $jobSpecProcessor;

    public function __construct(JobSpecProcessor $jobSpecProcessor)
    {
        $this->jobSpecProcessor = $jobSpecProcessor;
    }

    public function supports(ManifestInterface $manifest): bool
    {
        return $manifest instanceof JobInterface && !$manifest instanceof CronJobInterface;
    }

    protected function makeResource(ManifestInterface|JobInterface $manifest): Job
    {
        $job = new Job();
        $spec = $job->spec();

        $manifest->selector(new SelectorConfigurator($spec->selector()));

        $app = $manifest->app;
        $app::configureMeta($manifest, $job);
        $this->jobSpecProcessor->process($manifest, $spec);
        foreach (($spec->getSelector()?->matchLabels()->all() ?? []) as $name => $value) {
            $job->metadata()->labels()->add($name, $value);
        }

        $manifest->configureJob($job);

        if (null === $job->spec()->template()->spec()->getRestartPolicy()) {
            $job->spec()->template()->spec()->setRestartPolicy(
                RestartPolicy::Never->toString()
            );
        }

        $this->dispatcher->dispatch(new JobGeneratedEvent($manifest, $job, $app), JobGeneratedEvent::NAME);

        return $job;
    }

    protected function supportedClass(): string
    {
        return JobInterface::class;
    }
}
