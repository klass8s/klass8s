<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;

final readonly class StatefulSetGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.stateful_set';

    public function __construct(
        private StatefulSetInterface $manifest,
        private StatefulSet $sts,
        private AppInterface $app,
    ) {
    }

    public function manifest(): StatefulSetInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->sts;
    }

    public function statefulSet(): StatefulSet
    {
        return $this->sts;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
