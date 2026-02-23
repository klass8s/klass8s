<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress\Configurator;

use K8Schema\Api\Networking\V1\HTTPIngressPath;
use K8Schema\Api\Networking\V1\HTTPIngressRuleValue;
use K8Schema\Api\Networking\V1\IngressRule;
use Klass8s\App\AppInterface;
use Klass8s\Core\Ingress\Http\PathType;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;
use Klass8s\Registry\AppRegistry;

readonly class IngressRuleConfigurator
{
    public function __construct(
        private IngressRule $rule,
        private AppInterface $app,
        private ManifestClassReferenceConverter $referencesService,
        private AppRegistry $appRegistry,
    ) {
    }

    public function addPath(PathType $pathType, ?string $path = null): HttpIngressPathConfigurator
    {
        if (null === $path && in_array($pathType, [PathType::Exact, PathType::Prefix])) {
            throw new \InvalidArgumentException(
                'Argument $path cannot be null if $pathType is "Exact" or "Prefix"'
            );
        }

        $ingressPath = $this->createPath($pathType);
        $this->rule->http()->paths()->add($ingressPath);

        if ($path) {
            $ingressPath->setPath($path);
        }

        return new HttpIngressPathConfigurator(
            path: $ingressPath,
            app: $this->app,
            referencesService: $this->referencesService,
            appRegistry: $this->appRegistry
        );
    }

    private function createPath(PathType $pathType): HTTPIngressPath
    {
        return new HTTPIngressPath($pathType->toString());
    }
}
