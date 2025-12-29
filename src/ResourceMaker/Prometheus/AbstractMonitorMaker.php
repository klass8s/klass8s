<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Prometheus;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\LabelSelector;
use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use K8Schema\Collection\StringList;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Monitoring\Prometheus\Configurator\NamespaceSelectorConfigurator;
use Klass8s\Monitoring\Prometheus\MonitorApiResource;
use Klass8s\Monitoring\Prometheus\MonitorInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;
use Klass8s\ResourceMaker\Traits\ConfigureSelectorTrait;

abstract class AbstractMonitorMaker extends AbstractResourceMaker
{
    use ConfigureSelectorTrait;

    abstract protected function configureMonitor(MonitorInterface $manifest, \ArrayObject $data): void;

    protected function makeResource(ManifestInterface|MonitorInterface $manifest, AppInterface $app): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();
        $data['spec'] = new \ArrayObject();
        $data['spec']['selector'] = new LabelSelector();
        $data['spec']['namespaceSelector'] = new \ArrayObject();

        $monitor = new MonitorApiResource($data);
        $app->metadataHelper()->configureMeta($manifest, $monitor);
        $this->configureSelector($manifest, $data['spec']['selector']);

        $namespaceSelector = new NamespaceSelectorConfigurator($data['spec']['namespaceSelector']);
        $manifest->namespaceSelector($namespaceSelector);

        $podTargetLabels = new StringList();
        $manifest->podTargetLabels($podTargetLabels);

        if ($podTargetLabels->count() > 0) {
            $data['spec']['podTargetLabels'] = $podTargetLabels;
        }

        if ($jobLabel = $manifest->jobLabel()) {
            $data['spec']['jobLabel'] = $jobLabel;
        }

        if ($sampleLimit = $manifest->sampleLimit()) {
            $data['spec']['sampleLimit'] = $sampleLimit;
        }

        $this->configureMonitor($manifest, $data);

        return $monitor;
    }
}
