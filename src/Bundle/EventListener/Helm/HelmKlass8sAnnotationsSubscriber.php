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
        static $appNameAnnotation = Klass8sAnnotationsSubscriber::ANNOTATION_APP;
        static $deployPriorityAnnotation = Klass8sAnnotationsSubscriber::ANNOTATION_DEPLOY_PRIORITY;
        $manifest->metadata->annotations->$appNameAnnotation = $event->release->name;
        $manifest->metadata->annotations->$deployPriorityAnnotation = strval($event->release->deployPriority);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HelmManifestProcessedEvent::NAME => 'onManifestProcessed',
        ];
    }
}
