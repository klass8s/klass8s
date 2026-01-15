<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyManager;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractResourceMaker implements ResourceMakerInterface
{
    protected EventDispatcherInterface $dispatcher;

    abstract protected function supportedClass(): string;
    abstract protected function makeResource(ManifestInterface $manifest): APIResourceInterface;

    public function make(ManifestInterface $manifest): APIResourceInterface
    {
        $this->ensureSupportedInstance($manifest);

        return $this->makeResource($manifest);
    }

    public function supports(ManifestInterface $manifest): bool
    {
        $supportedClass = $this->supportedClass();

        return $manifest instanceof $supportedClass;
    }

    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    protected function ensureSupportedInstance(ManifestInterface $manifest): void
    {
        $supportedClass = $this->supportedClass();
        if (!$this->supports($manifest)) {
            throw new \InvalidArgumentException(sprintf('%s::process() only supports instances of %s, instance of %s was given', $this::class, $supportedClass, $manifest::className()));
        }
    }
}
