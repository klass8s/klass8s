<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\Attribute\NoDefaultMetadataLabels;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Core\MetadataConfigurator;
use Klass8s\Event\ProxyableMethodEventInterface;
use Klass8s\Manifest\Labels\LabelsGeneratorInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyManager;

class DefaultMetadataLabelsSubscriber extends AbstractManifestMethodSubscriber
{
    public function __construct(private readonly LabelsGeneratorInterface $labelsGenerator)
    {
    }

    protected function supports(ProxyableMethodEventInterface $event): bool
    {
        return 'metadata' === $event->methodName()
            && ($event->methodParams()['meta'] ?? null) instanceof MetadataConfigurator;
    }

    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        /** @var ManifestInterface $manifest */
        $manifest = $event->proxyable();
        if ($this->classHasNoLabelsAttribute(ProxyManager::unwrapClassName($manifest))) {
            return;
        }
        $labels = $this->labelsGenerator->forManifest($manifest);
        /** @var MetadataConfigurator $meta */
        $meta = $event->methodParams()['meta'];
        $meta->labels()->addAll($labels);
    }

    private function classHasNoLabelsAttribute(string $className): bool
    {
        $class = new \ReflectionClass($className);
        $attribute = AttributesUtil::fromClass($class, NoDefaultMetadataLabels::class);

        return null !== $attribute;
    }
}
