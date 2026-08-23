<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\Output\OutputDirectory;
use Klass8s\Proxy\ProxyFactory;

/**
 * The one supported way to add a dynamically-named manifest (a {@see ManifestTemplateInterface}
 * instance) to the build at render time. Spawning runs the instance through the same pipeline
 * a registry manifest goes through: it is proxied (method events, default labels, env-suffixed
 * methods), configured from the app's manifest config, handed to its resource maker (which
 * dispatches the per-kind generated event) and — unless a listener vetoes — added to the given
 * output directory, with the generic ManifestGeneratedEvent dispatched in between.
 *
 * The caller must set the app ({@see AbstractManifest::setApp()}) and the dynamic short name
 * before spawning.
 */
final class ManifestSpawner
{
    public function __construct(
        private readonly ProxyFactory $proxyFactory,
        private readonly ManifestProcessor $processor,
    ) {
    }

    public function spawn(ManifestInterface&ManifestTemplateInterface $template, OutputDirectory $outputDir): ManifestInterface
    {
        if (!isset($template->app)) {
            throw new \LogicException(sprintf('Manifest template "%s" must have its app set (call setApp()) before being spawned.', $template::className()));
        }

        /** @var ManifestInterface $proxy */
        $proxy = $this->proxyFactory->makeProxy($template);
        $this->processor->process($proxy, $outputDir);

        return $proxy;
    }
}
