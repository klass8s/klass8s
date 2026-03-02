<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy;

use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\OptionsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TargetRefsConfigurator;
use Klass8s\GatewayApi\BackendTLSPolicy\Configurator\TLSValidationConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface BackendTLSPolicyInterface extends ManifestInterface
{
    public function targetRefs(TargetRefsConfigurator $targetRefs): void;

    public function validation(TLSValidationConfigurator $validation): void;

    public function options(OptionsConfigurator $options): void;
}
