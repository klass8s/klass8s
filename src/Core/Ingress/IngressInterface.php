<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress;

use K8Schema\Api\Networking\V1\Ingress;
use Klass8s\Core\Ingress\Configurator\IngressBackendConfigurator;
use Klass8s\Core\Ingress\Configurator\IngressRulesConfigurator;
use Klass8s\Core\ManifestInterface;

interface IngressInterface extends ManifestInterface
{
    public function defaultBackend(IngressBackendConfigurator $factory): void;
    public function ingressClassName(): string|null;
    public function rules(IngressRulesConfigurator $rules): void;
    public function configureIngress(Ingress $ingress): void;
}
