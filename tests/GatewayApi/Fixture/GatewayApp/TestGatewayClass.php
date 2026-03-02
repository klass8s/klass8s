<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\GatewayClass\AbstractGatewayClass;

class TestGatewayClass extends AbstractGatewayClass
{
    public static function shortName(): string
    {
        return 'main';
    }

    public function controllerName(): string
    {
        return 'example.io/gateway-controller';
    }

    public function description(): ?string
    {
        return 'Test gateway class';
    }
}
