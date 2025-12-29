<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling\Configurator;

use K8Schema\Api\Autoscaling\V2\HorizontalPodAutoscalerBehavior;

readonly class BehaviorConfigurator
{
    public function __construct(private HorizontalPodAutoscalerBehavior $behavior)
    {
    }

    public function scaleUp(): HPAScalingRulesConfigurator
    {
        return new HPAScalingRulesConfigurator($this->behavior->scaleUp());
    }

    public function scaleDown(): HPAScalingRulesConfigurator
    {
        return new HPAScalingRulesConfigurator($this->behavior->scaleDown());
    }
}
