<?php

declare(strict_types=1);

namespace Klass8s\Core\Container;

use K8Schema\Api\Core\V1\Container;
use K8Schema\Collection\StringList;
use Klass8s\Core\Container\Env\EnvConfigurator;
use Klass8s\Core\Container\Image\ImagePullPolicy;
use Klass8s\Core\Container\Lifecycle\LifecycleConfigurator;
use Klass8s\Core\Container\Lifecycle\Probes\ProbesConfigurator;
use Klass8s\Core\Container\Ports\PortsConfigurator;
use Klass8s\Core\Container\Resources\ContainerResourcesInterface;
use Klass8s\Core\Container\Security\SecurityContextConfigurator;
use Klass8s\Core\Container\VolumeMount\VolumeMountsConfigurator;

trait ContainerTrait
{
    public function containerName(): string
    {
        return 'app';
    }

    public function args(StringList $args): void
    {
    }

    public function command(StringList $command): void
    {
    }

    public function env(EnvConfigurator $env): void
    {
    }

    public function volumeMounts(VolumeMountsConfigurator $mounts): void
    {
    }

    public function resources(ContainerResourcesInterface $resources): void
    {
    }

    public function ports(PortsConfigurator $ports): void
    {
    }

    public function lifecycle(LifecycleConfigurator $lifecycle): void
    {
    }

    public function probes(ProbesConfigurator $probes): void
    {
    }

    public function securityContext(SecurityContextConfigurator $context): void
    {
    }

    public function imagePullPolicy(): ImagePullPolicy
    {
        return ImagePullPolicy::IfNotPresent;
    }

    public function workingDir(): string|null
    {
        return null;
    }

    public function configureContainer(Container $container): void
    {
    }
}
