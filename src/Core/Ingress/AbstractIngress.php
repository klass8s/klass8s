<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress;

use K8Schema\Api\Networking\V1\Ingress;
use Klass8s\Core\Ingress\Configurator\IngressBackendConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractIngress extends AbstractManifest implements IngressInterface
{
    public function defaultBackend(IngressBackendConfigurator $factory): void
    {
    }

    public function ingressClassName(): ?string
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
