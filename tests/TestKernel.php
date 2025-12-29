<?php

declare(strict_types=1);

namespace Klass8s\Tests;

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
        ];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/klass8s_bundle_test_cache_'.uniqid();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/klass8s_bundle_test_logs';
    }

    /**
     * @phpstan-ignore missingType.iterableValue
     */
    protected function getKernelParameters(): array
    {
        return array_replace(parent::getKernelParameters(), [
            'kernel.project_dir' => __DIR__.'/../',
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
        });
    }
}
