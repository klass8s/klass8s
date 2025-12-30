<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject;

use Klass8s\Autoscaling\Keda\API\ScaledObject\Fallback;
use Klass8s\Autoscaling\Keda\API\ScaledObject\ScaledObject;
use Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\TriggerBuildersRegistry;
use Klass8s\Core\AbstractManifest;
use Klass8s\Core\Autoscaling\Configurator\BehaviorConfigurator;

abstract class AbstractScaledObject extends AbstractManifest implements ScaledObjectInterface
{
    public function pollingInterval(): int
    {
        return 30;
    }

    public function cooldownPeriod(): int
    {
        return 300;
    }

    public function idleReplicaCount(): int|null
    {
        return null;
    }

    public function minReplicaCount(): int
    {
        return 0;
    }

    public function maxReplicaCount(): int
    {
        return 100;
    }

    public function fallback(): Fallback|null
    {
        return null;
    }

    public function restoreToOriginalReplicaCount(): bool|null
    {
        return null;
    }

    public function hpaName(): string|null
    {
        return null;
    }

    public function behavior(BehaviorConfigurator $behavior): void
    {
    }

    public function triggers(TriggerBuildersRegistry $builders): iterable
    {
        return [];
    }

    public function configureScaledObject(ScaledObject $scaledObject): void
    {
    }

    public static function apiVersion(): string
    {
        return ScaledObject::API_VERSION;
    }

    public static function kind(): string
    {
        return ScaledObject::KIND;
    }
}
