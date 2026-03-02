<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway;

use Klass8s\GatewayApi\Gateway\Configurator\AddressesConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\InfrastructureConfigurator;
use Klass8s\GatewayApi\Gateway\Configurator\ListenersConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReference;

interface GatewayInterface extends ManifestInterface
{
    /**
     * Reference to the GatewayClass that this Gateway is an instance of.
     *
     * Can return either:
     * - a raw K8s resource name (string) — e.g. for external GatewayClasses like "istio"
     * - a ManifestClassReference — for referencing a Klass8s GatewayClass manifest by PHP class
     */
    public function gatewayClass(): string|ManifestClassReference;

    public function listeners(ListenersConfigurator $listeners): void;

    public function addresses(AddressesConfigurator $addresses): void;

    public function infrastructure(InfrastructureConfigurator $infrastructure): void;
}
