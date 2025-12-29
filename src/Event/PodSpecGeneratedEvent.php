<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Core\V1\PodSpec;
use Klass8s\Core\Pod\PodSpecInterface;

final readonly class PodSpecGeneratedEvent
{
    public const string NAME = 'klass8s.pod_spec.generated';

    public function __construct(public PodSpec $podSpec, public PodSpecInterface $builder)
    {
    }
}
