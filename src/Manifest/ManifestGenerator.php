<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\App\AppProcessor;
use Klass8s\Event\ManifestsProcessedEvent;
use Klass8s\Output\OutputDirectory;
use Klass8s\Renderer\RendererInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class ManifestGenerator
{
    private const string OUTPUT_DIRECTORY_APPS = 'apps';

    public function __construct(
        private AppProcessor $processor,
        private RendererInterface $renderer,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function generate(string ...$appAliases): OutputDirectory
    {
        $outputDir = new OutputDirectory(self::OUTPUT_DIRECTORY_APPS, $this->renderer);

        foreach ($appAliases as $alias) {
            $outputDir->addDirectory($this->processor->process($alias));
        }

        $this->dispatcher->dispatch(new ManifestsProcessedEvent($outputDir), ManifestsProcessedEvent::NAME);

        return $outputDir;
    }
}
