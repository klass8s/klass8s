<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use K8Schema\Collection\StringList;
use Klass8s\Collection\GenericList;
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

        $app = $manifest->app;

        $data['spec']['parentRefs'] = new GenericList();
        $parentRefs = new ParentRefsConfigurator($data['spec']['parentRefs'], $app);
        $manifest->parentRefs($parentRefs);

        $hostnames = new StringList();
        $manifest->hostnames($hostnames);
        if ($hostnames->count() > 0) {
            $data['spec']['hostnames'] = $hostnames;
        }

        $data['spec']['rules'] = new GenericList();
        $rules = new GRPCRouteRulesConfigurator($data['spec']['rules'], $app);
        $manifest->rules($rules);

        $resource = new GRPCRouteApiResource($data);
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new GRPCRouteGeneratedEvent($manifest, $resource, $app),
            GRPCRouteGeneratedEvent::NAME,
        );

        return $resource;
    }
}
