<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\Attribute\NoDefaultServiceSelector;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Event\ServiceGeneratedEvent;
use Klass8s\Manifest\Labels\LabelsGeneratorInterface;
use Klass8s\Proxy\ProxyManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class DefaultServiceSelectorSubscriber implements EventSubscriberInterface
{
    public function __construct(private LabelsGeneratorInterface $labelsGenerator)
    {
    }

    public function onServiceGenerated(ServiceGeneratedEvent $event): void
    {
        $manifest = $event->manifest();
        if ($this->classHasNoSelectorAttribute(ProxyManager::unwrapClassName($manifest))) {
            return;
        }
        $labels = $this->labelsGenerator->forManifest($manifest);
        $event->service()->spec()->selector()->addAll($labels);
    }

    public static function getSubscribedEvents(): array
    {
        return [ServiceGeneratedEvent::NAME => 'onServiceGenerated'];
    }

    private function classHasNoSelectorAttribute(string $className): bool
    {
        $class = new \ReflectionClass($className);
        $attribute = AttributesUtil::fromClass($class, NoDefaultServiceSelector::class);

        return null !== $attribute;
    }
}
