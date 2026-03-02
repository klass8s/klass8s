<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\GatewayGeneratedEvent;
use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\GatewayApi\Gateway\GatewayApiResource;
use Klass8s\GatewayApi\Gateway\GatewayInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class GatewayMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return GatewayInterface::class;
    }

    protected function makeResource(ManifestInterface|GatewayInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();
        $data['spec'] = new \ArrayObject();

        $data['spec']['gatewayClassName'] = $manifest->gatewayClassName();

        $data['spec']['listeners'] = new \ArrayObject();
        $listeners = new ListenersConfigurator($data['spec']['listeners']);
        $manifest->listeners($listeners);

        $addresses = $manifest->addresses();
        if ([] !== $addresses) {
            $data['spec']['addresses'] = $addresses;
        }

        $infrastructure = $manifest->infrastructure();
        if (null !== $infrastructure) {
            $data['spec']['infrastructure'] = $infrastructure;
        }

        $resource = new GatewayApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new GatewayGeneratedEvent($manifest, $resource, $app),
            GatewayGeneratedEvent::NAME,
        );

        return $resource;
    }
}
