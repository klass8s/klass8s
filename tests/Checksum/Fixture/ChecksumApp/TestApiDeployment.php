<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\ChecksumApp;

use Klass8s\Core\Container\Env\EnvConfigurator;
use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Deployment\AbstractContainerDeployment;

class TestApiDeployment extends AbstractContainerDeployment
{
    public static function shortName(): string
    {
        return 'api';
    }

    public function image(): Image
    {
        return Image::fromName('nginx')->setTag('1.29');
    }

    public function env(EnvConfigurator $env): void
    {
        $env->addConfigMapClass(TestConfigMap::class);
    }
}
