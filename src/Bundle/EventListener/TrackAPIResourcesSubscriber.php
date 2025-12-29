<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\Registry\APIResourceRegistry;
use Klass8s\Event\ConfigMapGeneratedEvent;
use Klass8s\Event\CronJobGeneratedEvent;
use Klass8s\Event\DeploymentGeneratedEvent;
use Klass8s\Event\JobGeneratedEvent;
use Klass8s\Event\ManifestGeneratedEventInterface;
use Klass8s\Event\SecretGeneratedEvent;
use Klass8s\Event\StatefulSetGeneratedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TrackAPIResourcesSubscriber implements EventSubscriberInterface
{
    private const EVENTS = [
        ConfigMapGeneratedEvent::NAME,
        CronJobGeneratedEvent::NAME,
        DeploymentGeneratedEvent::NAME,
        JobGeneratedEvent::NAME,
        SecretGeneratedEvent::NAME,
        StatefulSetGeneratedEvent::NAME,
    ];

    public function __construct(private APIResourceRegistry $registry)
    {
    }

    public function onManifestGenerated(ManifestGeneratedEventInterface $event): void
    {
        $this->registry->add($event->apiResource());
    }

    public static function getSubscribedEvents(): array
    {
        return array_fill_keys(self::EVENTS, ['onManifestGenerated', -1024]);
    }
}
