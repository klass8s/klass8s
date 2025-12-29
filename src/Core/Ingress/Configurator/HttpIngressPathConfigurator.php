<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress\Configurator;

use K8Schema\Api\Networking\V1\HTTPIngressPath;
use Klass8s\App\AppInterface;
use Klass8s\Registry\AppRegistry;
use Klass8s\Util\ManifestReferencesService;

readonly class HttpIngressPathConfigurator
{
    public function __construct(
        private HTTPIngressPath $path,
        private AppInterface $app,
        private ManifestReferencesService $referencesService,
        private AppRegistry $appRegistry
    ) {
    }

    public function backend(): IngressBackendConfigurator
    {
        return new IngressBackendConfigurator(
            app: $this->app,
            appRegistry: $this->appRegistry,
            referencesService: $this->referencesService,
            backend: $this->path->backend()
        );
    }
}
