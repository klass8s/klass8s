<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

use Klass8s\Autoscaling\Attribute\Autoscale;
use Klass8s\Core\Deployment\DeploymentInterface;

final class AttributeAutoscalePolicy implements AutoscalePolicyInterface
{
    public function decide(Autoscale $attribute, DeploymentInterface $manifest): AutoscaleDecision
    {
        return AutoscaleDecision::enabled($attribute->minReplicas, $attribute->maxReplicas);
    }
}
