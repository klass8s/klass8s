<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling;

use Klass8s\Bundle\Klass8sBundle;
use Proximity\Bundle\ProximityBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class AutoscaleTestKernel extends Kernel
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
        // Unique per process AND per instance: the pre-commit hook runs several phpunit
        // processes concurrently, and a shared compile dir trips Symfony's container lock.
        return sys_get_temp_dir().'/klass8s_autoscale_test_cache_'.getmypid().'_'.uniqid('', true);
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/klass8s_autoscale_test_logs';
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
                'namespace_prefix' => 'Klass8s\Tests\Bundle\Autoscaling\Fixture',
                'manifests_dir' => sys_get_temp_dir().'/klass8s_autoscale_test_manifests',
            ]);

            $container->register(Fixture\AutoscaleApp\AutoscaleApp::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\AutoscaleApp\ScaledWorkerDeployment::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\AutoscaleApp\InheritingWorkerDeployment::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\AutoscaleApp\PlainDeployment::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\AutoscaleApp\FanOutWorkerDeployment::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\TestLagTriggerResolver::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);

            $container->register(Fixture\TestFanOutTriggerResolver::class)
                ->setAutowired(true)
                ->setAutoconfigured(true);
        });
    }
}
