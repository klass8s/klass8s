<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Event\BackendTLSPolicyGeneratedEvent;
use Klass8s\Collection\GenericList;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyApiResource;
use Klass8s\GatewayApi\BackendTLSPolicy\BackendTLSPolicyInterface;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\OptionsConfigurator;
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

        $app = $manifest->app;

        $data['spec']['targetRefs'] = new GenericList();
        $targetRefs = new TargetRefsConfigurator($data['spec']['targetRefs'], $app);
        $manifest->targetRefs($targetRefs);

        $data['spec']['validation'] = new \ArrayObject();
        $validation = new TLSValidationConfigurator($data['spec']['validation'], $app);
        $manifest->validation($validation);

        $options = new \ArrayObject();
        $manifest->options(new OptionsConfigurator($options));
        if ($options->count() > 0) {
            $data['spec']['options'] = $options;
        }

        $resource = new BackendTLSPolicyApiResource($data);
        $app::configureMeta($manifest, $resource);

        $this->dispatcher->dispatch(
            new BackendTLSPolicyGeneratedEvent($manifest, $resource, $app),
            BackendTLSPolicyGeneratedEvent::NAME,
        );

        return $resource;
    }
}
