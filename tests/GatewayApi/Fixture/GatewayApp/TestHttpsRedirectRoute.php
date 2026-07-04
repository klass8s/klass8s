<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\HTTPRoute\AbstractHttpsRedirectRoute;

class TestHttpsRedirectRoute extends AbstractHttpsRedirectRoute
{
    public function gatewayName(): string
    {
        return 'gateway-main';
    }

    public function gatewayNamespace(): ?string
    {
        return 'edge';
    }
}
