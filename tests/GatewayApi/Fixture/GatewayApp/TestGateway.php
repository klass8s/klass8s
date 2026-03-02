<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\GatewayApi\Enum\ProtocolType;
use Klass8s\GatewayApi\Gateway\AbstractGateway;
use Klass8s\GatewayApi\Gateway\Configurator\AddressesConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\Manifest\Reference\ManifestClassReference;

class TestGateway extends AbstractGateway
{
    public static function shortName(): string
    {
        return 'main';
    }

    public function gatewayClass(): string|ManifestClassReference
    {
        return new ManifestClassReference('gateway', TestGatewayClass::class);
    }

    public function listeners(ListenersConfigurator $listeners): void
    {
        $listeners->add('http')
            ->port(80)
            ->protocol(ProtocolType::HTTP)
            ->hostname('*.example.com')
            ->allowedRoutes()
                ->fromSameNamespace()
                ->allowHTTPRoutes();

        $listeners->add('https')
            ->port(443)
            ->protocol(ProtocolType::HTTPS)
            ->hostname('*.example.com')
            ->tls()
                ->terminate()
                ->certificateRef('wildcard-cert');
    }

    public function addresses(AddressesConfigurator $addresses): void
    {
        $addresses->addIPAddress('10.0.0.1');
    }
}
