<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute;

use Klass8s\GatewayApi\AbstractGatewayApiResource;
use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteRulesConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractHTTPRoute extends AbstractManifest implements HTTPRouteInterface
{
    final public static function kind(): string
    {
        return HTTPRouteApiResource::KIND;
    }

    public static function apiVersion(): string
    {
        return AbstractGatewayApiResource::API_VERSION;
    }

    public function parentRefs(ParentRefsConfigurator $parentRefs): void
    {
    }

    public function hostnames(): array
    {
        return [];
    }

    public function rules(HTTPRouteRulesConfigurator $rules): void
    {
    }
}
