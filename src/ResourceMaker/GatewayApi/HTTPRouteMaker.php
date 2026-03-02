<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use K8Schema\Collection\StringList;
use Klass8s\Collection\GenericList;
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
        $rules = new HTTPRouteRulesConfigurator($data['spec']['rules'], $app);
        $manifest->rules($rules);

        $resource = new HTTPRouteApiResource($data);
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new HTTPRouteGeneratedEvent($manifest, $resource, $app),
            HTTPRouteGeneratedEvent::NAME,
        );

        return $resource;
    }
}
