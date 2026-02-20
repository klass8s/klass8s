<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\ManifestGeneratedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class Klass8sAnnotationsSubscriber implements EventSubscriberInterface
{
    public const string ANNOTATION_DEPLOY_PRIORITY = 'klass8s.dev/deploy-priority';
    public const string ANNOTATION_APP = 'klass8s.dev/app';

    public function onManifestGenerated(ManifestGeneratedEvent $event): void
    {
        $app = $event->manifest()->app;
        $annotations = $event->apiResource()->metadata()->annotations();
        $annotations
            ->add(self::ANNOTATION_APP, $app->alias())
            ->add(self::ANNOTATION_DEPLOY_PRIORITY, $app->deployPriority())
        ;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ManifestGeneratedEvent::class => 'onManifestGenerated',
        ];
    }
}
