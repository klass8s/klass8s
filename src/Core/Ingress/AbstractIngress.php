<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress;

use K8Schema\Api\Networking\V1\Ingress;
use Klass8s\Core\AbstractManifest;
use Klass8s\Core\Ingress\Configurator\IngressBackendConfigurator;

abstract class AbstractIngress extends AbstractManifest implements IngressInterface
{
    public function defaultBackend(IngressBackendConfigurator $factory): void
    {
    }

    public function ingressClassName(): string|null
    {
        return null;
    }

    public function configureIngress(Ingress $ingress): void
    {
    }

    final public static function kind(): string
    {
        return Ingress::KIND;
    }

    public static function apiVersion(): string
    {
        return Ingress::API_VERSION;
    }
}
