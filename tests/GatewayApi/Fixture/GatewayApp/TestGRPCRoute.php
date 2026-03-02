<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\GRPCRoute\AbstractGRPCRoute;
use Klass8s\GatewayApi\GRPCRoute\Configurator\GRPCRouteRulesConfigurator;

class TestGRPCRoute extends AbstractGRPCRoute
{
    public static function shortName(): string
    {
        return 'grpc';
    }

    public function parentRefs(ParentRefsConfigurator $parentRefs): void
    {
        $parentRefs->add('gateway-main')
            ->sectionName('https');
    }

    public function hostnames(): array
    {
        return ['grpc.example.com'];
    }

    public function rules(GRPCRouteRulesConfigurator $rules): void
    {
        $rule = $rules->add();
        $rule->name('user-service');
        $rule->match()
            ->method('mypackage.UserService', 'GetUser');
        $rule->backendRef()
            ->service('user-grpc', 9090)
            ->weight(100);
    }
}
