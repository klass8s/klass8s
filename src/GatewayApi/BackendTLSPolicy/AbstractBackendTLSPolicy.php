<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy;

use Klass8s\GatewayApi\AbstractGatewayApiResource;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\OptionsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TargetRefsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TLSValidationConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractBackendTLSPolicy extends AbstractManifest implements BackendTLSPolicyInterface
{
    final public static function kind(): string
    {
        return BackendTLSPolicyApiResource::KIND;
    }

    public static function apiVersion(): string
    {
        return AbstractGatewayApiResource::API_VERSION;
    }

    public function targetRefs(TargetRefsConfigurator $targetRefs): void
    {
    }

    public function validation(TLSValidationConfigurator $validation): void
    {
    }

    public function options(OptionsConfigurator $options): void
    {
    }
}
