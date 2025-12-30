<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\Metrics;

use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\AbstractTriggerBuilder;

abstract class AbstractStandardMetricTrigger extends AbstractTriggerBuilder
{
    private const string CONTAINER_NAME = 'containerName';
    private const string VALUE = 'value';

    public function value(int $value): static
    {
        return $this->set(self::VALUE, $value);
    }

    public function containerName(string $containerName): static
    {
        return $this->set(self::CONTAINER_NAME, $containerName);
    }

    public static function withValue(int $value): static
    {
        $instance = new static();
        $instance->value($value);

        return $instance;
    }

    protected function requiredKeys(): array
    {
        return [self::VALUE];
    }
}
