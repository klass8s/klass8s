<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest;

use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\ScaledObjectInterface;

interface ScalableWorkloadInterface
{
    public function scaledObject(): ScaledObjectInterface;
}
