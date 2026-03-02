<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\GatewayClassGeneratedEvent;
use Klass8s\GatewayApi\GatewayClass\GatewayClassApiResource;
use Klass8s\GatewayApi\GatewayClass\GatewayClassInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class GatewayClassMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return GatewayClassInterface::class;
    }

    protected function makeResource(ManifestInterface|GatewayClassInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();
        $data['spec'] = new \ArrayObject();

        $data['spec']['controllerName'] = $manifest->controllerName();

        if (null !== $description = $manifest->description()) {
            $data['spec']['description'] = $description;
        }

        if (null !== $parametersRef = $manifest->parametersRef()) {
            $data['spec']['parametersRef'] = $parametersRef;
        }

        $resource = new GatewayClassApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new GatewayClassGeneratedEvent($manifest, $resource, $app),
            GatewayClassGeneratedEvent::NAME,
        );

        return $resource;
    }
}
