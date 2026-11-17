<?php

declare(strict_types=1);

namespace Klass8s\Tests\Autoscaling\Fixture;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\AutoscaleContext;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;
use Klass8s\Autoscaling\ScalingTriggerResolverInterface;

/**
 * A resolver written in PHP, where the interface's `Trigger|array` return type holds the
 * elements to nothing: a trigger, and then something that is not one.
 */
final class LooseTriggerResolver implements ScalingTriggerResolverInterface
{
    public function supports(ScalingTriggerAttributeInterface $attribute): bool
    {
        return true;
    }

    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): Trigger|array
    {
        return ['cpu' => new Trigger('cpu'), 'memory' => new \stdClass()]; // @phaenon-ignore: what PHP lets a resolver return
    }
}
