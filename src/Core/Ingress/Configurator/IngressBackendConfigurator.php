<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress\Configurator;

use K8Schema\Api\Networking\V1\IngressBackend;
use K8Schema\Api\Networking\V1\IngressServiceBackend;
use K8Schema\Api\Networking\V1\ServiceBackendPort;
use Klass8s\App\AppInterface;
use Klass8s\Core\Service\ServiceInterface;
use Klass8s\Manifest\Reference\ManifestClassReference;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;
use Klass8s\Registry\AppRegistry;

readonly class IngressBackendConfigurator
{
    public function __construct(
        private AppInterface $app,
        private AppRegistry $appRegistry,
        private ManifestClassReferenceConverter $referencesService,
        private IngressBackend $backend
    ) {
    }

    public function fromServiceNameAndPort(string $serviceName, int|string $servicePort): void
    {
        $serviceBackend = new IngressServiceBackend($serviceName);


        if (is_numeric($servicePort)) {
            $serviceBackend->port()->setNumber($servicePort);
        } else {
            $serviceBackend->port()->setName($servicePort);
        }

        $this->backend->setService($serviceBackend);
    }

    /**
     * @param class-string<ServiceInterface> $serviceClass
     */
    public function fromServiceClassAndPort(string $serviceClass, int|string $servicePort): void
    {
        $serviceName = $this->app->resourceNameFromManifestClass($serviceClass, ServiceInterface::class);
        $this->fromServiceNameAndPort($serviceName, $servicePort);
    }

    public function fromManifestReference(ManifestClassReference $manifestReference): void
    {
        $resource = $this->referencesService->toTypedLocalObjectReference($manifestReference);
        $this->backend->setResource($resource);
    }

    public function withExternalApp(string $appAlias): self
    {
        return new self($this->appRegistry->get($appAlias), $this->appRegistry, $this->referencesService, $this->backend);
    }
}
