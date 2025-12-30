<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\Cron;

use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\AbstractTriggerBuilder;

class CronTrigger extends AbstractTriggerBuilder
{
    private const string TIMEZONE = 'timezone';
    private const string START = 'start';
    private const string END = 'end';
    private const string DESIRED_REPLICAS = 'desiredReplicas';

    public function timezone(\DateTimeZone $timezone): static
    {
        return $this->set(self::TIMEZONE, $timezone->getName());
    }

    public function start(string $cronSchedule): static
    {
        return $this->set(self::START, $cronSchedule);
    }

    public function end(string $cronSchedule): static
    {
        return $this->set(self::END, $cronSchedule);
    }

    public function desiredReplicas(int $desiredReplicas): static
    {
        return $this->set(self::DESIRED_REPLICAS, strval($desiredReplicas));
    }

    protected function requiredKeys(): array
    {
        return [
            self::TIMEZONE,
            self::START,
            self::END,
            self::DESIRED_REPLICAS,
        ];
    }

    protected static function type(): string
    {
        return 'cron';
    }
}
