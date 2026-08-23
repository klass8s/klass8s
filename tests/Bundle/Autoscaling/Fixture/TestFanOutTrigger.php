<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;

#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class TestFanOutTrigger implements ScalingTriggerAttributeInterface
{
    /**
     * @param list<string> $queues
     */
    public function __construct(
        public array $queues,
        public int $lagThreshold,
    ) {
    }
}
