<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum;

use Klass8s\Bundle\Klass8sBundle;
use Klass8s\Tests\Checksum\Fixture\VolumeApp\InitConfigMap;
use Klass8s\Tests\Checksum\Fixture\VolumeApp\MigrateInitContainer;
use Klass8s\Tests\Checksum\Fixture\VolumeApp\MountedConfigMap;
use Klass8s\Tests\Checksum\Fixture\VolumeApp\VolumeApp;
use Klass8s\Tests\Checksum\Fixture\VolumeApp\VolumeDeployment;
use Proximity\Bundle\ProximityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * A kernel whose fixture content is a constructor argument, so one test can render the same project
 * twice and compare — with the same input (determinism) or with one value changed (the checksum has
 * to move). Every instance compiles into its own cache directory: two renders must not share a
 * container, or the second one would only prove that a cache was warm.
 */
final class VolumeChecksumTestKernel extends Kernel
{
    public const string DEFAULT_ESO_SECRET_NAME = 'volumes-tls-from-eso';

    public function __construct(
        private readonly string $configValue,
        private readonly string $initConfigValue = 'migrate',
        private readonly string $esoSecretName = self::DEFAULT_ESO_SECRET_NAME,
        private readonly bool $markSecretExternal = true,
        private readonly string $cacheSalt = '',
    ) {
        parent::__construct('test', true);
    }

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new ProximityBundle(),
            new Klass8sBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        $key = hash('xxh128', implode('|', [
            $this->configValue,
            $this->initConfigValue,
            $this->esoSecretName,
            $this->markSecretExternal ? '1' : '0',
            $this->cacheSalt,
        ]));

        return sys_get_temp_dir().'/klass8s_volume_checksum_cache_'.$key;
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/klass8s_volume_checksum_logs';
    }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    protected function getKernelParameters(): array
    {
        return array_replace(parent::getKernelParameters(), [
            'kernel.project_dir' => __DIR__,
            'kernel.environment' => 'test',
            'kernel.runtime_mode.web' => false,
            'kernel.runtime_mode.cli' => true,
            'env(APP_SECRET)' => 'volume-checksum-test-secret',
        ]);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'test' => true,
                'secret' => '%env(APP_SECRET)%',
            ]);

            $container->loadFromExtension('klass8s', [
                'code_dir' => __DIR__.'/Fixture',
                'namespace_prefix' => 'Klass8s\Tests\Checksum\Fixture',
                'manifests_dir' => sys_get_temp_dir().'/klass8s_volume_checksum_manifests',
            ]);

            $container->register(VolumeApp::class)->setAutowired(true)->setAutoconfigured(true);
            $container->register(MigrateInitContainer::class)->setAutowired(true)->setAutoconfigured(true);

            $container->register(MountedConfigMap::class)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setArgument('$value', $this->configValue);

            $container->register(InitConfigMap::class)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setArgument('$value', $this->initConfigValue);

            $container->register(VolumeDeployment::class)
                ->setAutowired(true)
                ->setAutoconfigured(true)
                ->setArgument('$esoSecretName', $this->esoSecretName)
                ->setArgument('$markSecretExternal', $this->markSecretExternal);
        });
    }
}
