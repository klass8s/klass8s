<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\VolumeApp;

use Klass8s\Core\Container\AbstractContainer;
use Klass8s\Core\Container\Env\EnvConfigurator;
use Klass8s\Core\Container\Image\Image;

class MigrateInitContainer extends AbstractContainer
{
    public function containerName(): string
    {
        return 'migrate';
    }

    public function image(): Image
    {
        return Image::fromName('busybox')->setTag('1.37');
    }

    public function env(EnvConfigurator $env): void
    {
        $env->addConfigMapClass(InitConfigMap::class);
    }
}
