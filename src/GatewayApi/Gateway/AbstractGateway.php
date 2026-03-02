<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway;

use Klass8s\GatewayApi\AbstractGatewayApiResource;
use Klass8s\GatewayApi\Gateway\Configurator\AddressesConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\InfrastructureConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractGateway extends AbstractManifest implements GatewayInterface
{
    final public static function kind(): string
    {
        return GatewayApiResource::KIND;
    }

    public static function apiVersion(): string
    {
        return AbstractGatewayApiResource::API_VERSION;
    }

    public function listeners(ListenersConfigurator $listeners): void
    {
    }

    public function addresses(AddressesConfigurator $addresses): void
    {
    }

    public function infrastructure(InfrastructureConfigurator $infrastructure): void
    {
    }
}
