<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\AutoscaleContext;
use Klass8s\Autoscaling\ScalingTriggerResolverInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;

final class TestFanOutTriggerResolver implements ScalingTriggerResolverInterface
{
    public function supports(ScalingTriggerAttributeInterface $attribute): bool
    {
        return $attribute instanceof TestFanOutTrigger;
    }

    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): array
    {
        \assert($attribute instanceof TestFanOutTrigger);

        $triggers = [];
        foreach ($attribute->queues as $queue) {
            $trigger = new Trigger('test-lag');
            $trigger->metadata->add('queue', $queue);
            $trigger->metadata->add('lagThreshold', $attribute->lagThreshold);
            $triggers[] = $trigger;
        }

        return $triggers;
    }
}
