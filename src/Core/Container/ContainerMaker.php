<?php

declare(strict_types=1);

namespace Klass8s\Core\Container;

use Proximity\ProxyInterface;
use K8Schema\Api\Core\V1\Container;
use K8Schema\Api\Core\V1\VolumeList;
use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Env\EnvConfigurator;
use Klass8s\Core\Container\Env\ExternalConfigurationRegistry;
use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Container\Lifecycle\LifecycleConfigurator;
use Klass8s\Core\Container\Lifecycle\Probes\ProbesConfigurator;
use Klass8s\Core\Container\Ports\PortsConfigurator;
use Klass8s\Core\Container\Resources\ResourcesConfigurator;
use Klass8s\Core\Container\Security\SecurityContextConfigurator;
use Klass8s\Core\Container\VolumeMount\VolumeMountsConfigurator;
use Klass8s\Event\ContainerGeneratedEvent;
use Klass8s\Middleware\ContainerImageMiddlewareInterface;
use Klass8s\Proxy\ProxyFactory;
use Klass8s\Registry\AppRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class ContainerMaker implements ContainerMakerInterface
{
    /**
     * @param ContainerImageMiddlewareInterface[] $middlewares
     */
    public function __construct(
        private AppRegistry $appRegistry,
        private EventDispatcherInterface $dispatcher,
        private ProxyFactory $proxyFactory,
        private ExternalConfigurationRegistry $externalConfigurations,
        private iterable $middlewares
    ) {
    }

    public function make(ContainerInterface $builder, VolumeList $volumes, AppInterface $app): Container
    {
        if (!($builder instanceof ProxyInterface)) {
            $builder = $this->proxyFactory->makeProxy($builder);
        }

        $container = new Container($builder->containerName());

        $builder->args($container->args());
        $builder->command($container->command());

        $env = new EnvConfigurator(
            $container->env(),
            $container->envFrom(),
            $app,
            $this->appRegistry,
            $this->dispatcher,
            $this->externalConfigurations,
        );
        $builder->env($env);

        $mounts = new VolumeMountsConfigurator($volumes, $container->volumeMounts());
        $builder->volumeMounts($mounts);

        $resources = new ResourcesConfigurator($container->resources());
        $builder->resources($resources);

        $ports = new PortsConfigurator($container->ports());
        $builder->ports($ports);

        $lifecycle = new LifecycleConfigurator($container->lifecycle());
        $builder->lifecycle($lifecycle);

        $probes = new ProbesConfigurator(
            $container->livenessProbe(),
            $container->readinessProbe(),
            $container->startupProbe()
        );
        $builder->probes($probes);

        $image = $builder->image();
        $this->applyMiddlewares($image, $app);
        $container->setImage($image->fullName());
        $pullPolicy = $builder->imagePullPolicy();
        if (null !== $pullPolicy) {
            $container->setImagePullPolicy($pullPolicy->toString());
        }

        $workingDir = $builder->workingDir();
        if (null !== $workingDir) {
            $container->setWorkingDir($workingDir);
        }

        $securityContext = new SecurityContextConfigurator($container->securityContext());
        $builder->securityContext($securityContext);

        $builder->configureContainer($container);

        $this->dispatcher->dispatch(new ContainerGeneratedEvent($container, $builder), ContainerGeneratedEvent::NAME);

        return $container;
    }

    private function applyMiddlewares(Image $image, AppInterface $app): void
    {
        foreach ($this->middlewares as $middleware) {
            $middleware->apply($image, $app);
        }
    }
}
