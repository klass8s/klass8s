<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use K8Schema\Collection\StringList;
use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\Enum\HTTPMethod;
use Klass8s\GatewayApi\Enum\HTTPPathModifierType;
use Klass8s\GatewayApi\HTTPRoute\AbstractHTTPRoute;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteRulesConfigurator;

class TestHTTPRoute extends AbstractHTTPRoute
{
    public static function shortName(): string
    {
        return 'api';
    }

    public function parentRefs(ParentRefsConfigurator $parentRefs): void
    {
        $parentRefs->add('gateway-main')
            ->sectionName('https');
    }

    public function hostnames(StringList $hostnames): void
    {
        $hostnames->add('api.example.com');
    }

    public function rules(HTTPRouteRulesConfigurator $rules): void
    {
        $rule = $rules->add();
        $rule->name('api-v1');
        $rule->match()
            ->pathPrefix('/api/v1')
            ->method(HTTPMethod::GET);
        $rule->match()
            ->pathPrefix('/api/v1')
            ->method(HTTPMethod::POST);
        $rule->backendRef()
            ->service('api-service', 8080)
            ->weight(90);
        $rule->backendRef()
            ->service('api-canary', 8080)
            ->weight(10);
        $rule->timeout('30s');
        $rule->backendTimeout('10s');

        $redirect = $rules->add();
        $redirect->name('old-api-redirect');
        $redirect->match()
            ->pathPrefix('/api/v0');
        $redirect->filter()
            ->requestRedirect(
                statusCode: 301,
                pathType: HTTPPathModifierType::ReplacePrefixMatch,
                pathValue: '/api/v1',
            );
    }
}
