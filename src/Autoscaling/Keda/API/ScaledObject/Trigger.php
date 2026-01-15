<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\API\ScaledObject;

use K8Schema\Collection\StringMap;
use Klass8s\Core\Autoscaling\Metric\TargetType;
use Klass8s\Value\StringMapProxy;

class Trigger implements \JsonSerializable
{
    public ?AuthenticationReference $authenticationRef = null;
    public readonly StringMap $metadata;
    public ?TargetType $metricType = null;
    public bool $useCachedMetrics = false;

    public function __construct(public readonly string $type)
    {
        $this->metadata = StringMapProxy::make(new StringMap());
    }


    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'metricType' => $this->metricType?->value,
            'authenticationRef' => $this->authenticationRef,
            'metadata' => $this->metadata,
        ];
    }
}
