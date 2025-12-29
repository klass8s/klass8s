<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\CronJob\CronJobInterface;

readonly class CronJobGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.cronJob';

    public function __construct(private CronJobInterface $manifest, private CronJob $cronJob, private AppInterface $app)
    {
    }

    public function manifest(): CronJobInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->cronJob;
    }

    public function cronJob(): CronJob
    {
        return $this->cronJob;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
