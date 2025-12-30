<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\API\ScaledObject;

final readonly class Fallback implements \JsonSerializable
{
    public function __construct(public int $failureThreshold, public int $replicas)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'failureThreshold' => $this->failureThreshold,
            'replicas' => $this->replicas,
        ];
    }
}
