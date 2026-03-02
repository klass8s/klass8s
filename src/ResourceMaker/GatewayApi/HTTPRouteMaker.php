<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\HTTPRouteGeneratedEvent;
use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteRulesConfigurator;
use Klass8s\GatewayApi\HTTPRoute\HTTPRouteApiResource;
use Klass8s\GatewayApi\HTTPRoute\HTTPRouteInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class HTTPRouteMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return HTTPRouteInterface::class;
    }

    protected function makeResource(ManifestInterface|HTTPRouteInterface $manifest): APIResourceInterface
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
        $rules = new HTTPRouteRulesConfigurator($data['spec']['rules']);
        $manifest->rules($rules);

        $resource = new HTTPRouteApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new HTTPRouteGeneratedEvent($manifest, $resource, $app),
            HTTPRouteGeneratedEvent::NAME,
        );

        return $resource;
    }
}
