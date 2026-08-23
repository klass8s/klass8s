<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling;

use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\Nats\NatsJetStreamTrigger;
use PHPUnit\Framework\TestCase;

class NatsJetStreamTriggerTest extends TestCase
{
    public function testBuildsTheKedaTrigger(): void
    {
        $trigger = NatsJetStreamTrigger::forConsumer('MEDIA_CONVERSION', 'conversion_video')
            ->monitoringEndpoint('nats.operations.svc.cluster.local:8222')
            ->account('TEQ')
            ->lagThreshold(16)
            ->build();

        self::assertSame('nats-jetstream', $trigger->type);
        $metadata = $trigger->metadata->all();
        self::assertSame('MEDIA_CONVERSION', $metadata['stream']);
        self::assertSame('conversion_video', $metadata['consumer']);
        self::assertSame('nats.operations.svc.cluster.local:8222', $metadata['natsServerMonitoringEndpoint']);
        self::assertSame('TEQ', $metadata['account']);
        self::assertSame('16', $metadata['lagThreshold']);
    }

    public function testMissingRequiredKeyFailsTheBuild(): void
    {
        $trigger = NatsJetStreamTrigger::forConsumer('MEDIA_CONVERSION', 'conversion_video')
            ->lagThreshold(16);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/natsServerMonitoringEndpoint/');
        $trigger->build();
    }
}
