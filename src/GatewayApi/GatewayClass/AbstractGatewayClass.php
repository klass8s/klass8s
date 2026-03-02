<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GatewayClass;

use Klass8s\GatewayApi\AbstractGatewayApiResource;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractGatewayClass extends AbstractManifest implements GatewayClassInterface
{
    final public static function kind(): string
    {
        return GatewayClassApiResource::KIND;
    }

    public static function apiVersion(): string
    {
        return AbstractGatewayApiResource::API_VERSION;
    }

    public function description(): ?string
    {
        return null;
    }

    public function parametersRef(): ?array
    {
        return null;
    }
}
