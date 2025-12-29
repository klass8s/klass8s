<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\EventListener\AutoSetResourcesSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class AutoSetResourcesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $autoSetResources = $container->getParameter('klass8s.auto_set_resources');
        if (!$autoSetResources) {
            $container->removeDefinition(AutoSetResourcesSubscriber::class);
        }
    }
}
