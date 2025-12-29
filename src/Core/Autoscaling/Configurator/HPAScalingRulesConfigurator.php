<?php

declare(strict_types=1);

namespace Klass8s\Core\Autoscaling\Configurator;

use K8Schema\Api\Autoscaling\V2\HPAScalingPolicy;
use K8Schema\Api\Autoscaling\V2\HPAScalingRules;
use Klass8s\Core\Autoscaling\ScalingPolicy;
use Klass8s\Core\Autoscaling\SelectPolicy;

final readonly class HPAScalingRulesConfigurator
{
    public const int STABILIZATION_WINDOW_SECONDS_MIN_VALUE = 0;
    public const int STABILIZATION_WINDOW_SECONDS_MAX_VALUE = 3600;

    public function __construct(private HPAScalingRules $rules)
    {
    }

    public function addPolicy(ScalingPolicy $scalingPolicy): self
    {
        $policy = new HPAScalingPolicy(periodSeconds: $scalingPolicy->periodSeconds, type: $scalingPolicy->type, value: $scalingPolicy->amountOfChange);
        $this->rules->policies()->add($policy);

        return $this;
    }

    public function stabilizationWindowSeconds(int $seconds): self
    {
        if ($seconds < self::STABILIZATION_WINDOW_SECONDS_MIN_VALUE || $seconds > self::STABILIZATION_WINDOW_SECONDS_MAX_VALUE) {
            throw new \InvalidArgumentException(
                sprintf(
                    '$value must be greater than or equal to %d and less than or equal to %d',
                    self::STABILIZATION_WINDOW_SECONDS_MIN_VALUE,
                    self::STABILIZATION_WINDOW_SECONDS_MAX_VALUE
                )
            );
        }

        $this->rules->setStabilizationWindowSeconds($seconds);

        return $this;
    }

    public function selectPolicy(SelectPolicy $policy): self
    {
        $this->rules->setSelectPolicy($policy->value);

        return $this;
    }
}
