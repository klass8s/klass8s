<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\Service;
use K8Schema\Api\Core\V1\ServicePortList;
use K8Schema\APIResourceInterface;
use Klass8s\Core\Service\Configurator\ServicePortsConfigurator;
use Klass8s\Core\Service\Configurator\ServiceTypeConfigurator;
use Klass8s\Core\Service\ServiceInterface;
use Klass8s\Event\ServiceGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;

final class ServiceMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return ServiceInterface::class;
    }

    protected function makeResource(ManifestInterface|ServiceInterface $manifest): APIResourceInterface
    {
        $service = new Service();
        $app = $manifest->app;
        $app::configureMeta($manifest, $service);

        $clusterIp = $manifest->clusterIP();
        if (null !== $clusterIp) {
            $service->spec()->setClusterIP($clusterIp);
        }

        $trafficDistribution = $manifest->trafficDistribution();
        if (null !== $trafficDistribution) {
            $service->spec()->setTrafficDistribution($trafficDistribution->value);
        }
        $type = new ServiceTypeConfigurator($service);
        $manifest->type($type);

        $ports = new ServicePortsConfigurator($service->spec()->ports());
        $manifest->ports($ports);
        self::validatePorts($service->spec()->ports(), $manifest);

        $manifest->selector($service->spec()->selector());
        $manifest->configureService($service);

        $this->dispatcher->dispatch(new ServiceGeneratedEvent($manifest, $service, $app), ServiceGeneratedEvent::NAME);

        return $service;
    }

    private static function validatePorts(ServicePortList $ports, ServiceInterface $manifest): void
    {
        $ports = $ports->all();
        if (count($ports) > 1) {
            foreach ($ports as $port) {
                if (null === $port->getName()) {
                    throw new \LogicException(sprintf('Multiple ports specified for service "%s", therefore all ports must have name.', $manifest->computedFullName));
                }
            }
        }
    }
}
