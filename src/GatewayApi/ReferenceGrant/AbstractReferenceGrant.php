<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant;

use Klass8s\GatewayApi\AbstractGatewayApiResource;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantFromConfigurator;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantToConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractReferenceGrant extends AbstractManifest implements ReferenceGrantInterface
{
    final public static function kind(): string
    {
        return ReferenceGrantApiResource::KIND;
    }

    public static function apiVersion(): string
    {
        return AbstractGatewayApiResource::API_VERSION;
    }

    public function from(ReferenceGrantFromConfigurator $from): void
    {
    }

    public function to(ReferenceGrantToConfigurator $to): void
    {
    }
}
