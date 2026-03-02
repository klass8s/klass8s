<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Registry\AppRegistry;
use Klass8s\Event\GatewayGeneratedEvent;
use Klass8s\GatewayApi\Gateway\Configurator\AddressesConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\InfrastructureConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\GatewayApi\Gateway\GatewayApiResource;
use Klass8s\GatewayApi\Gateway\GatewayInterface;
use Klass8s\GatewayApi\GatewayClass\GatewayClassInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReference;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class GatewayMaker extends AbstractResourceMaker
{
    public function __construct(private readonly AppRegistry $appRegistry)
    {
    }

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

        $gatewayClass = $manifest->gatewayClass();
        if ($gatewayClass instanceof ManifestClassReference) {
            $owningApp = $this->appRegistry->get($gatewayClass->appAlias());
            $data['spec']['gatewayClassName'] = $owningApp->resourceNameFromManifestClass(
                $gatewayClass->className(),
                GatewayClassInterface::class,
            );
        } else {
            $data['spec']['gatewayClassName'] = $gatewayClass;
        }

        $app = $manifest->app;

        $data['spec']['listeners'] = new \ArrayObject();
        $listeners = new ListenersConfigurator($data['spec']['listeners'], $app);
        $manifest->listeners($listeners);

        $addresses = new \ArrayObject();
        $manifest->addresses(new AddressesConfigurator($addresses));
        if ($addresses->count() > 0) {
            $data['spec']['addresses'] = $addresses;
        }

        $infrastructure = new \ArrayObject();
        $manifest->infrastructure(new InfrastructureConfigurator($infrastructure));
        if ($infrastructure->count() > 0) {
            $data['spec']['infrastructure'] = $infrastructure;
        }

        $resource = new GatewayApiResource($data);
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new GatewayGeneratedEvent($manifest, $resource, $app),
            GatewayGeneratedEvent::NAME,
        );

        return $resource;
    }
}
