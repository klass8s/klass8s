<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledJob\Configurator;

use Klass8s\Autoscaling\Keda\API\ScaledJob\Rollout;

final readonly class RolloutConfigurator
{
    public function __construct(private Rollout $rollout)
    {
    }

    public function setStrategy(string $strategy): self
    {
        $this->rollout->setStrategy($strategy);

        return $this;
    }

    public function setPropagationPolicy(string $propagationPolicy): self
    {
        $this->rollout->setPropagationPolicy($propagationPolicy);

        return $this;
    }
}
