<?php

declare(strict_types=1);

namespace Klass8s\Core\Container;

use K8Schema\Api\Core\V1\Container;
use K8Schema\Api\Core\V1\VolumeList;
use Klass8s\App\AppInterface;

interface ContainerMakerInterface
{
    public function make(ContainerInterface $builder, VolumeList $volumes, AppInterface $app): Container;
}
