<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\Checksum\ChecksumsService;
use Klass8s\Bundle\Registry\APIResourceRegistry;
use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\Api\Apps\V1\StatefulSet;
use K8Schema\Api\Batch\V1\CronJob;
use K8Schema\Api\Batch\V1\Job;
use Klass8s\Event\ManifestsProcessedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CheksumsSubscriber implements EventSubscriberInterface
{
    private const WORKLOAD_KINDS = [
        Deployment::KIND,
        Job::KIND,
        CronJob::KIND,
        StatefulSet::KIND,
    ];

    public function __construct(private ChecksumsService $service, private APIResourceRegistry $registry)
    {
    }

    public function onManifestsProcessed(): void
    {
        foreach ($this->registry->all() as $kind => $apiResource) {
            if (!in_array($kind, self::WORKLOAD_KINDS, true)) {
                continue;
            }

            $this->service->calculateChecksums($apiResource);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ManifestsProcessedEvent::NAME => ['onManifestsProcessed', -1024],
        ];
    }
}
