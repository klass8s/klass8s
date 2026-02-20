<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener\Helm;

use Klass8s\Bundle\EventListener\Klass8sAnnotationsSubscriber;
use Klass8s\Event\Helm\HelmManifestProcessedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class HelmKlass8sAnnotationsSubscriber implements EventSubscriberInterface
{
    public function onManifestProcessed(HelmManifestProcessedEvent $event): void
    {
        $manifest = $event->resource;
        $manifest->metadata->annotations[Klass8sAnnotationsSubscriber::ANNOTATION_APP] = $event->release->name;
        $manifest->metadata->annotations[Klass8sAnnotationsSubscriber::ANNOTATION_DEPLOY_PRIORITY] = strval($event->release->deployPriority);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HelmManifestProcessedEvent::NAME => 'onManifestProcessed',
        ];
    }
}
