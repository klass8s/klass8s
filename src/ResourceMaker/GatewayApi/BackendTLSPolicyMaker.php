<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\BackendTLSPolicyGeneratedEvent;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyApiResource;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyInterface;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TargetRefsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TLSValidationConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\AbstractResourceMaker;

final class BackendTLSPolicyMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return BackendTLSPolicyInterface::class;
    }

    protected function makeResource(ManifestInterface|BackendTLSPolicyInterface $manifest): APIResourceInterface
    {
        $data = new \ArrayObject();
        $data['apiVersion'] = $manifest::apiVersion();
        $data['kind'] = $manifest::kind();
        $data['metadata'] = new ObjectMeta();
        $data['spec'] = new \ArrayObject();

        $data['spec']['targetRefs'] = new \ArrayObject();
        $targetRefs = new TargetRefsConfigurator($data['spec']['targetRefs']);
        $manifest->targetRefs($targetRefs);

        $data['spec']['validation'] = new \ArrayObject();
        $validation = new TLSValidationConfigurator($data['spec']['validation']);
        $manifest->validation($validation);

        $options = $manifest->options();
        if ([] !== $options) {
            $data['spec']['options'] = $options;
        }

        $resource = new BackendTLSPolicyApiResource($data);
        $app = $manifest->app;
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new BackendTLSPolicyGeneratedEvent($manifest, $resource, $app),
            BackendTLSPolicyGeneratedEvent::NAME,
        );

        return $resource;
    }
}
