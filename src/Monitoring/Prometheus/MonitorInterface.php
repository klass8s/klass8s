<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus;

use K8Schema\Collection\StringList;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\ManifestInterface;
use Klass8s\Monitoring\Prometheus\Configurator\NamespaceSelectorConfigurator;

interface MonitorInterface extends ManifestInterface
{
    public function jobLabel(): string|null;
    public function namespaceSelector(NamespaceSelectorConfigurator $namespaceSelector): void;
    public function podTargetLabels(StringList $labels): void;
    public function sampleLimit(): int|null;
    public function selector(SelectorConfigurator $selector): void;
}
