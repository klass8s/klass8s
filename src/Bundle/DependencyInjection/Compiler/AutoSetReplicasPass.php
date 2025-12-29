<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\EventListener\AutoSetReplicasSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class AutoSetReplicasPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $autoSetReplicas = $container->getParameter('klass8s.auto_set_replicas');
        if (!$autoSetReplicas) {
            $container->removeDefinition(AutoSetReplicasSubscriber::class);
        }
    }
}
