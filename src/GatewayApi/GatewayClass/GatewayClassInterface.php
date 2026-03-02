<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GatewayClass;

use Klass8s\Manifest\ManifestInterface;

interface GatewayClassInterface extends ManifestInterface
{
    /**
     * Domain-prefixed string identifier of the controller managing Gateways of this class.
     * Example: "example.io/gateway-controller".
     */
    public function controllerName(): string;

    public function description(): ?string;

    public function parametersRef(): ?ParametersRef;
}
