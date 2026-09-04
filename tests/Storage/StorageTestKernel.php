<?php

declare(strict_types=1);

namespace Klass8s\Tests\Storage;

use Klass8s\Bundle\Klass8sBundle;
use Proximity\Bundle\ProximityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class StorageTestKernel extends Kernel
{
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
        return sys_get_temp_dir().'/klass8s_storage_test_cache_'.spl_object_id($this);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/klass8s_storage_test_logs';
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
            'env(APP_SECRET)' => bin2hex(random_bytes(32)),
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
                'namespace_prefix' => 'Klass8s\Tests\Storage\Fixture',
                'manifests_dir' => sys_get_temp_dir().'/klass8s_storage_test_manifests',
            ]);

            foreach ([
                Fixture\StorageApp\StorageApp::class,
                Fixture\StorageApp\TestStorageClass::class,
                Fixture\StorageApp\TestMinimalStorageClass::class,
                Fixture\StorageApp\TestEksAutoModeGp3StorageClass::class,
            ] as $class) {
                $container->register($class)
                    ->setAutowired(true)
                    ->setAutoconfigured(true);
            }
        });
    }
}
