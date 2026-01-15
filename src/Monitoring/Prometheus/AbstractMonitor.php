<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus;

use K8Schema\Collection\StringList;
use Klass8s\Manifest\AbstractManifest;
use Klass8s\Monitoring\Prometheus\Configurator\NamespaceSelectorConfigurator;

abstract class AbstractMonitor extends AbstractManifest implements MonitorInterface
{
    public static function apiVersion(): string
    {
        return 'monitoring.coreos.com/v1';
    }

    public function jobLabel(): ?string
    {
        return null;
    }

    public function namespaceSelector(NamespaceSelectorConfigurator $namespaceSelector): void
    {
    }

    public function podTargetLabels(StringList $labels): void
    {
    }

    public function sampleLimit(): ?int
    {
        return null;
    }
}
