<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\AutoscaleContext;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\ScalingTriggerResolverInterface;

final class TestLagTriggerResolver implements ScalingTriggerResolverInterface
{
    public function supports(ScalingTriggerAttributeInterface $attribute): bool
    {
        return $attribute instanceof TestLagTrigger;
    }

    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): Trigger
    {
        \assert($attribute instanceof TestLagTrigger);

        $trigger = new Trigger('test-lag');
        $trigger->metadata->add('queue', $attribute->queue);
        $trigger->metadata->add('lagThreshold', $attribute->lagThreshold);
        $trigger->metadata->add('env', $context->env);

        return $trigger;
    }
}
