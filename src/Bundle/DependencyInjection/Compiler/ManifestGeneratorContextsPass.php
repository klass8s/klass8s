<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context\ContextInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class ManifestGeneratorContextsPass implements CompilerPassInterface
{
    public const string CONTEXT_TAG = 'klass8s.manifest_generator.context';

    public function process(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ContextInterface::class)
            ->addTag(self::CONTEXT_TAG);
    }
}
