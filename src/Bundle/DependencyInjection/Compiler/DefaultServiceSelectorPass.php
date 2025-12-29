<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\EventListener\DefaultServiceSelectorSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class DefaultServiceSelectorPass implements CompilerPassInterface
{
    public const string PARAM = 'klass8s.set_default_service_selector';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter(self::PARAM)) {
            $container->removeDefinition(DefaultServiceSelectorSubscriber::class);
        }
    }
}
