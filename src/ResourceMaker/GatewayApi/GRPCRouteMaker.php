<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\GRPCRouteGeneratedEvent;
use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\GRPCRoute\Configurator\GRPCRouteRulesConfigurator;
use Klass8s\GatewayApi\GRPCRoute\GRPCRouteApiResource;
use Klass8s\GatewayApi\GRPCRoute\GRPCRouteInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class GRPCRouteMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return GRPCRouteInterface::class;
    }

    protected function makeResource(ManifestInterface|GRPCRouteInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();
        $data['spec'] = new \ArrayObject();

        $data['spec']['parentRefs'] = new \ArrayObject();
        $parentRefs = new ParentRefsConfigurator($data['spec']['parentRefs']);
        $manifest->parentRefs($parentRefs);

        $hostnames = $manifest->hostnames();
        if ([] !== $hostnames) {
            $data['spec']['hostnames'] = $hostnames;
        }

        $data['spec']['rules'] = new \ArrayObject();
        $rules = new GRPCRouteRulesConfigurator($data['spec']['rules']);
        $manifest->rules($rules);

        $resource = new GRPCRouteApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new GRPCRouteGeneratedEvent($manifest, $resource, $app),
            GRPCRouteGeneratedEvent::NAME,
        );

        return $resource;
    }
}
