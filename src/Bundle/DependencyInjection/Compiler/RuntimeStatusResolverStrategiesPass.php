<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Runtime\ManifestStatusResolverStrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class RuntimeStatusResolverStrategiesPass implements CompilerPassInterface
{
    public const string TAG = 'klass8s.runtime_status_resolver.strategy';

    public function process(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ManifestStatusResolverStrategyInterface::class)
            ->addTag(self::TAG);
    }
}
