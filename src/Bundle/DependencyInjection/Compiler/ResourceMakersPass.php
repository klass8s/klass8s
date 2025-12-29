<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\ResourceMaker\ResourceMakerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ResourceMakersPass implements CompilerPassInterface
{
    public const string RESOURCE_MAKER_TAG = 'klass8s.resource_maker';

    public function process(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(ResourceMakerInterface::class)
            ->addTag(ResourceMakersPass::RESOURCE_MAKER_TAG);
    }
}
