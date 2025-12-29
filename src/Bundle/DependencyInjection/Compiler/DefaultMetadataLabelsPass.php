<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\EventListener\DefaultMetadataLabelsSubscriber;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class DefaultMetadataLabelsPass implements CompilerPassInterface
{
    public const string PARAM = 'klass8s.default_metadata_labels';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->getParameter(self::PARAM)) {
            $container->removeDefinition(DefaultMetadataLabelsSubscriber::class);
        }
    }
}
