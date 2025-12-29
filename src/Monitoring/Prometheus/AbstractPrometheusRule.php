<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus;

use Klass8s\Core\AbstractManifest;

abstract class AbstractPrometheusRule extends AbstractManifest implements PrometheusRuleInterface
{
    public static function apiVersion(): string
    {
        return 'monitoring.coreos.com/v1';
    }

    public static function kind(): string
    {
        return 'PrometheusRule';
    }
}
