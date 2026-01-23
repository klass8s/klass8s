<?php

declare(strict_types=1);

namespace Klass8s\Bundle;

use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuilderInterface;
use Klass8s\Bridge\Helm\Release\HelmReleaseInterface;
use Klass8s\Bundle\Checksum\Calculator\ChecksumCalculatorInterface;
use Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context\ContextInterface;
use Klass8s\Bundle\DependencyInjection\Compiler\AppsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\AutoSetReplicasPass;
use Klass8s\Bundle\DependencyInjection\Compiler\AutoSetResourcesPass;
use Klass8s\Bundle\DependencyInjection\Compiler\DisableChecksumGenerationPass;
use Klass8s\Bundle\DependencyInjection\Compiler\HelmReleasesPass;
use Klass8s\Bundle\DependencyInjection\Compiler\ManifestsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\RemoveAutowiredAppsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\SetAppConfigPass;
use Klass8s\Bundle\DependencyInjection\Klass8sExtension;
use Klass8s\Bundle\EnvManagement\DIContainerRegistry;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Middleware\ContainerImageMiddlewareInterface;
use Klass8s\ResourceMaker\ResourceMakerInterface;
use Klass8s\Runtime\ManifestStatusResolverStrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class Klass8sBundle extends AbstractBundle
{
    public const string TAG_APP = 'klass8s.app';
    public const string TAG_MANIFEST = 'klass8s.manifest';
    public const string TAG_RESOURCE_MAKER = 'klass8s.resource_maker';
    public const string TAG_MG_CONTEXT = 'klass8s.manifest_generator.context';
    public const string TAG_IMAGE_MIDDLEWARE = 'klass8s.middleware.container_image';
    public const string TAG_CHECKSUM_CALCULATOR = 'klass8s.checksum_calculator';
    public const string TAG_MANIFEST_RUNTIME_STATUS_RESOLVER = 'klass8s.manifest.runtime_status_resolver.strategy';
    public const string TAG_KEDA_TRIGGER_BUILDER = 'klass8s.autoscaling.keda.trigger_builder';
    public const string TAG_HELM_RELEASE = 'klass8s.helm.release';

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $this->setupAutoconfiguration($container);

        $container
            ->addCompilerPass(new AppsPass())
            ->addCompilerPass(new DisableChecksumGenerationPass())
            ->addCompilerPass(new HelmReleasesPass())
            ->addCompilerPass(pass: new SetAppConfigPass(), type: PassConfig::TYPE_OPTIMIZE, priority: -8)
            ->addCompilerPass(pass: new ManifestsPass(), type: PassConfig::TYPE_OPTIMIZE, priority: -16)
            ->addCompilerPass(pass: new RemoveAutowiredAppsPass(), type: PassConfig::TYPE_REMOVE)
            ->addCompilerPass(pass: new AutoSetReplicasPass(), priority: -32)
            ->addCompilerPass(pass: new AutoSetResourcesPass(), priority: -32)
        ;
    }

    public function getContainerExtension(): Klass8sExtension
    {
        return new Klass8sExtension();
    }

    public function setContainer(?ContainerInterface $container = null): void
    {
        $this->container = $container;
        if (null !== $container) {
            DIContainerRegistry::set($container);
        }
    }

    private function setupAutoconfiguration(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(AppInterface::class)
            ->addTag(self::TAG_APP);

        $container->registerForAutoconfiguration(ManifestInterface::class)
            ->addTag(self::TAG_MANIFEST);

        $container->registerForAutoconfiguration(ResourceMakerInterface::class)
            ->addTag(self::TAG_RESOURCE_MAKER);

        $container->registerForAutoconfiguration(ContextInterface::class)
            ->addTag(self::TAG_MG_CONTEXT);

        $container->registerForAutoconfiguration(ContainerImageMiddlewareInterface::class)
            ->addTag(self::TAG_IMAGE_MIDDLEWARE);

        $container
            ->registerForAutoconfiguration(ChecksumCalculatorInterface::class)
            ->addTag(self::TAG_CHECKSUM_CALCULATOR);

        $container
            ->registerForAutoconfiguration(ManifestStatusResolverStrategyInterface::class)
            ->addTag(self::TAG_MANIFEST_RUNTIME_STATUS_RESOLVER);

        $container
            ->registerForAutoconfiguration(TriggerBuilderInterface::class)
            ->addTag(self::TAG_KEDA_TRIGGER_BUILDER);

        $container
            ->registerForAutoconfiguration(HelmReleaseInterface::class)
            ->addTag(self::TAG_HELM_RELEASE);
    }
}
