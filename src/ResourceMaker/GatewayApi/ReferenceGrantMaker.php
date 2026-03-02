<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\ReferenceGrantGeneratedEvent;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantFromConfigurator;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantToConfigurator;
use Klass8s\GatewayApi\ReferenceGrant\ReferenceGrantApiResource;
use Klass8s\GatewayApi\ReferenceGrant\ReferenceGrantInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class ReferenceGrantMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return ReferenceGrantInterface::class;
    }

    protected function makeResource(ManifestInterface|ReferenceGrantInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();

        $data['spec'] = new \ArrayObject();

        $data['spec']['from'] = new \ArrayObject();
        $from = new ReferenceGrantFromConfigurator($data['spec']['from']);
        $manifest->from($from);

        $data['spec']['to'] = new \ArrayObject();
        $to = new ReferenceGrantToConfigurator($data['spec']['to']);
        $manifest->to($to);

        $resource = new ReferenceGrantApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new ReferenceGrantGeneratedEvent($manifest, $resource, $app),
            ReferenceGrantGeneratedEvent::NAME,
        );

        return $resource;
    }
}
