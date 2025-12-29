<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Batch\V1\Job;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\Job\JobInterface;

readonly class JobGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.job';

    public function __construct(private JobInterface $manifest, private Job $job, private AppInterface $app)
    {
    }

    public function manifest(): JobInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->job;
    }

    public function job(): Job
    {
        return $this->job;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
