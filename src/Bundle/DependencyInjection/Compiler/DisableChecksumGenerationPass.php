<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\EventListener\CheksumsSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class DisableChecksumGenerationPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->hasParameter('klass8s.auto_generate_checksums') && false === $container->getParameter('klass8s.auto_generate_checksums')) {
            $container->removeDefinition(CheksumsSubscriber::class);
        }
    }
}
