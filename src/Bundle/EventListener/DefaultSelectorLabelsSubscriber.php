<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use Klass8s\Bundle\Attribute\NoDefaultSelectorLabels;
use Klass8s\Bundle\EventListener\AbstractManifestMethodSubscriber;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\LabelsGeneratorInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;
use Klass8s\Event\ProxyableMethodEventInterface;
use Klass8s\Util\ClassName;

class DefaultSelectorLabelsSubscriber extends AbstractManifestMethodSubscriber
{
    public function __construct(private readonly LabelsGeneratorInterface $labelsGenerator)
    {
    }

    protected function supports(ProxyableMethodEventInterface $event): bool
    {
        $manifest = $event->proxyable();

        return ($manifest instanceof DeploymentInterface || $manifest instanceof StatefulSetInterface)
            && 'selector' === $event->methodName();
    }

    protected function beforeMethod(ProxyableMethodEventInterface $event): void
    {
        /** @var DeploymentInterface|StatefulSetInterface $manifest */
        $manifest = $event->proxyable();
        if ($this->classHasNoSelectorAttribute(ClassName::real($manifest))) {
            return;
        }

        /** @var SelectorConfigurator $selector */
        $selector = $event->methodParams()['selector'];
        $labels = $this->labelsGenerator->byManifestInstance($manifest);
        $selector->addLabels($labels);
    }

    private function classHasNoSelectorAttribute(string $className): bool
    {
        $class = new \ReflectionClass($className);
        $attribute = AttributesUtil::fromClass($class, NoDefaultSelectorLabels::class);

        return null !== $attribute;
    }
}
