<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Networking\V1\Ingress;
use K8Schema\Api\Networking\V1\IngressBackend;
use K8Schema\APIResourceInterface;
use Klass8s\Core\Ingress\Configurator\IngressBackendConfigurator;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;
use Klass8s\Core\Ingress\IngressInterface;
use Klass8s\Event\IngressGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;
use Klass8s\Registry\AppRegistry;

final class IngressMaker extends AbstractResourceMaker
{
    public function __construct(
        private readonly ManifestClassReferenceConverter $referencesService,
        private readonly AppRegistry $appRegistry
    ) {
    }

    protected function makeResource(ManifestInterface|IngressInterface $manifest): APIResourceInterface
    {
        $ingress = new Ingress();
        $app = $manifest->app;
        $app::configureMeta($manifest, $ingress);

        $specDefaultBackend = $ingress->spec()->getDefaultBackend() ?? new IngressBackend();
        $backendConfigurator = new IngressBackendConfigurator(
            app: $app,
            appRegistry: $this->appRegistry,
            referencesService: $this->referencesService,
            backend: $specDefaultBackend
        );
        $manifest->defaultBackend($backendConfigurator);
        if ([] !== array_filter($specDefaultBackend->jsonSerialize()->getArrayCopy())) {
            $ingress->spec()->setDefaultBackend($specDefaultBackend);
        }

        $rules = new IngressRulesConfigurator(
            rules: $ingress->spec()->rules(),
            app: $app,
            referencesService: $this->referencesService,
            appRegistry: $this->appRegistry,
        );
        $manifest->rules($rules);

        $ingressClassName = $manifest->ingressClassName();
        if (null !== $ingressClassName) {
            $ingress->spec()->setIngressClassName($ingressClassName);
        }

        $manifest->configureIngress($ingress);

        $this->dispatcher->dispatch(new IngressGeneratedEvent($manifest, $ingress, $app), IngressGeneratedEvent::NAME);

        return $ingress;
    }

    protected function supportedClass(): string
    {
        return IngressInterface::class;
    }
}
