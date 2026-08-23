<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
final readonly class TestLagTrigger implements ScalingTriggerAttributeInterface
{
    public function __construct(
        public string $queue,
        public int $lagThreshold,
    ) {
    }
}
