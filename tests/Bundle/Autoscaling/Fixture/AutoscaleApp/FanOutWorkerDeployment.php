<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture\AutoscaleApp;

use Klass8s\Autoscaling\Attribute\Autoscale;
use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Deployment\AbstractContainerDeployment;
use Klass8s\Tests\Bundle\Autoscaling\Fixture\TestFanOutTrigger;

#[Autoscale(maxReplicas: 5)]
#[TestFanOutTrigger(queues: ['queue-one', 'queue-two'], lagThreshold: 4)]
class FanOutWorkerDeployment extends AbstractContainerDeployment
{
    public static function shortName(): string
    {
        return 'fanout-worker';
    }

    public function image(): Image
    {
        return Image::fromName('nginx')->setTag('1.29');
    }
}
