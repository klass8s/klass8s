<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Networking\V1\Ingress;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\Ingress\Configurator\IngressBackendConfigurator;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;
use Klass8s\Core\Ingress\IngressInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Event\IngressGeneratedEvent;
use Klass8s\Registry\AppRegistry;
use Klass8s\Util\ManifestReferencesService;

class IngressMaker extends AbstractResourceMaker
{
    public function __construct(
        private readonly ManifestReferencesService $referencesService,
        private readonly AppRegistry $appRegistry
    ) {
    }

    protected function makeResource(ManifestInterface|IngressInterface $manifest, AppInterface $app): APIResourceInterface
    {
        $ingress = new Ingress();
        $app->metadataHelper()->configureMeta($manifest, $ingress);

        $backendConfigurator = new IngressBackendConfigurator(
            app: $app,
            appRegistry: $this->appRegistry,
            referencesService: $this->referencesService,
            backend: $ingress->spec()->defaultBackend()
        );
        $manifest->defaultBackend($backendConfigurator);

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

    protected function supportsClass(): string
    {
        return IngressInterface::class;
    }
}
