<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Event\Helm\HelmManifestProcessedEvent;
use Klass8s\Event\ManifestGeneratedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class StripNamespaceSubscriber implements EventSubscriberInterface
{
    private \ReflectionProperty $namespaceProperty;

    public function __construct()
    {
        $this->namespaceProperty = new \ReflectionProperty(
            \K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta::class,
            'namespace',
        );
    }

    public function onManifestGenerated(ManifestGeneratedEvent $event): void
    {
        $this->namespaceProperty->setValue($event->apiResource()->metadata(), null);
    }

    public function onHelmManifestProcessed(HelmManifestProcessedEvent $event): void
    {
        unset($event->resource->metadata->namespace);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ManifestGeneratedEvent::NAME => 'onManifestGenerated',
            HelmManifestProcessedEvent::NAME => 'onHelmManifestProcessed',
        ];
    }
}
