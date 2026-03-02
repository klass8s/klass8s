<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\ReferenceGrant\AbstractReferenceGrant;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantFromConfigurator;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantToConfigurator;

class TestReferenceGrant extends AbstractReferenceGrant
{
    public static function shortName(): string
    {
        return 'allow-routes';
    }

    public function from(ReferenceGrantFromConfigurator $from): void
    {
        $from->allowHTTPRoutes('frontend-ns');
        $from->allowGateways('infra-ns');
    }

    public function to(ReferenceGrantToConfigurator $to): void
    {
        $to->allowServices();
        $to->allowSecrets('tls-cert');
    }
}
