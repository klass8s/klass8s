<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Prometheus;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Monitoring\Prometheus\Configurator\RuleGroupsConfigurator;
use Klass8s\Monitoring\Prometheus\PrometheusRuleApiResource;
use Klass8s\Monitoring\Prometheus\PrometheusRuleInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

class PrometheusRuleMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return PrometheusRuleInterface::class;
    }

    protected function makeResource(ManifestInterface|PrometheusRuleInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();

        $data['metadata'] = new ObjectMeta();
        $prometheusRule = new PrometheusRuleApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $prometheusRule);

        $data['spec'] = new \ArrayObject();
        $data['spec']['groups'] = new \ArrayObject();

        $groupsConfigurator = new RuleGroupsConfigurator($data['spec']['groups']);
        $manifest->groups($groupsConfigurator);

        return $prometheusRule;
    }
}
