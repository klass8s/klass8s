<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture\AutoscaleApp;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Deployment\AbstractContainerDeployment;

class PlainDeployment extends AbstractContainerDeployment
{
    public static function shortName(): string
    {
        return 'plain';
    }

    public function image(): Image
    {
        return Image::fromName('nginx')->setTag('1.29');
    }

    public function replicas(): int
    {
        return 2;
    }
}
