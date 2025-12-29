<?php

declare(strict_types=1);

namespace Klass8s\Bundle;

use Klass8s\App\AppInterface;
use Klass8s\Bundle\Checksum\Calculator\ChecksumCalculatorInterface;
use Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context\ContextInterface;
use Klass8s\Bundle\DependencyInjection\Compiler\AppsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\AutoSetReplicasPass;
use Klass8s\Bundle\DependencyInjection\Compiler\AutoSetResourcesPass;
use Klass8s\Bundle\DependencyInjection\Compiler\ChecksumCalculatorPass;
use Klass8s\Bundle\DependencyInjection\Compiler\DisableChecksumGenerationPass;
use Klass8s\Bundle\DependencyInjection\Compiler\ManifestGeneratorContextsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\ManifestsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\MiddlewarePass;
use Klass8s\Bundle\DependencyInjection\Compiler\RemoveAutowiredAppsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\ResourceMakersPass;
use Klass8s\Bundle\DependencyInjection\Compiler\RuntimeStatusResolverStrategiesPass;
use Klass8s\Bundle\DependencyInjection\Compiler\SetAppConfigPass;
use Klass8s\Bundle\DependencyInjection\Klass8sExtension;
use Klass8s\Bundle\EnvManagement\DIContainerRegistry;
use Klass8s\Core\ManifestInterface;
use Klass8s\Middleware\ContainerImageMiddlewareInterface;
use Klass8s\ResourceMaker\ResourceMakerInterface;
use Klass8s\Runtime\ManifestStatusResolverStrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Klass8sBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->setupAutoconfiguration($container);

        $container
            ->addCompilerPass(new AppsPass())
            ->addCompilerPass(new RuntimeStatusResolverStrategiesPass())
            ->addCompilerPass(new DisableChecksumGenerationPass())
            ->addCompilerPass(pass: new SetAppConfigPass(), type: PassConfig::TYPE_OPTIMIZE, priority: -8)
            ->addCompilerPass(pass: new ManifestsPass(), type: PassConfig::TYPE_OPTIMIZE, priority: -16)
            ->addCompilerPass(pass: new ManifestGeneratorContextsPass())
            ->addCompilerPass(pass: new MiddlewarePass())
            ->addCompilerPass(pass: new ResourceMakersPass())
            ->addCompilerPass(pass: new RemoveAutowiredAppsPass(), type: PassConfig::TYPE_REMOVE)
            ->addCompilerPass(pass: new AutoSetReplicasPass(), priority: -32)
            ->addCompilerPass(pass: new AutoSetResourcesPass(), priority: -32)
        ;
    }

    public function getContainerExtension(): Klass8sExtension
    {
        return new Klass8sExtension();
    }

    public function setContainer(ContainerInterface|null $container = null): void
    {
        $this->container = $container;
        if (null !== $container) {
            DIContainerRegistry::set($container);
        }
    }

    private function setupAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(AppInterface::class)
            ->addTag(AppsPass::APP_TAG);

        $container->registerForAutoconfiguration(ManifestInterface::class)
            ->addTag(ManifestsPass::MANIFEST_TAG);

        $container->registerForAutoconfiguration(ResourceMakerInterface::class)
            ->addTag(ResourceMakersPass::RESOURCE_MAKER_TAG);

        $container->registerForAutoconfiguration(ContextInterface::class)
            ->addTag(ManifestGeneratorContextsPass::CONTEXT_TAG);

        $container->registerForAutoconfiguration(ContainerImageMiddlewareInterface::class)
            ->addTag(MiddlewarePass::IMAGE_MIDDLEWARE_TAG);

        $container
            ->registerForAutoconfiguration(ChecksumCalculatorInterface::class)
            ->addTag(ChecksumCalculatorPass::CHECKSUM_CALCULATOR_TAG);

        $container
            ->registerForAutoconfiguration(ManifestStatusResolverStrategyInterface::class)
            ->addTag(RuntimeStatusResolverStrategiesPass::TAG);
    }
}
