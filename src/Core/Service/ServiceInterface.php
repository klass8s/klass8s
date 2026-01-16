<?php

declare(strict_types=1);

namespace Klass8s\Core\Service;

use K8Schema\Api\Core\V1\Service;
use K8Schema\Collection\StringMap;
use Klass8s\Core\Service\Configurator\ServicePortsConfigurator;
use Klass8s\Core\Service\Configurator\ServiceTypeConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface ServiceInterface extends ManifestInterface
{
    public function clusterIP(): ?string;
    public function ports(ServicePortsConfigurator $ports): void;
    public function selector(StringMap $selector): void;
    public function type(ServiceTypeConfigurator $type): void;
    public function configureService(Service $service): void;
    public function trafficDistribution(): ?TrafficDistribution;
}
