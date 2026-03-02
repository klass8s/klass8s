<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\BackendTLSPolicy\AbstractBackendTLSPolicy;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TargetRefsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TLSValidationConfigurator;

class TestBackendTLSPolicy extends AbstractBackendTLSPolicy
{
    public static function shortName(): string
    {
        return 'backend-tls';
    }

    public function targetRefs(TargetRefsConfigurator $targetRefs): void
    {
        $targetRefs->addService('api-service', 'https');
    }

    public function validation(TLSValidationConfigurator $validation): void
    {
        $validation
            ->hostname('api.internal')
            ->caCertificateFromConfigMap('api-ca-cert')
            ->subjectAltNameHostname('api.internal');
    }
}
