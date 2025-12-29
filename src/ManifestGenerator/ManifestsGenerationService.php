<?php

declare(strict_types=1);

namespace Klass8s\ManifestGenerator;

use Klass8s\App\AppProcessor;
use Klass8s\Dumper\AppDumper;
use Klass8s\Event\ManifestsDumpedEvent;
use Klass8s\Event\ManifestsProcessedEvent;
use Klass8s\Event\ManifestsRenderedEvent;
use Klass8s\Registry\AppRegistry;
use Klass8s\Renderer\RendererInterface;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class ManifestsGenerationService
{
    public function __construct(
        private AppRegistry $registry,
        private AppProcessor $processor,
        private AppDumper $dumper,
        private RendererInterface $renderer,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function dumpAll(string $directoryPath): void
    {
        $directoryPath = self::validDirectoryPath($directoryPath);

        foreach ($this->registry->aliases() as $alias) {
            $dir = $directoryPath.DIRECTORY_SEPARATOR.$alias;
            $this->dumper->dump($alias, $dir);
        }

        $this->dispatcher->dispatch(new ManifestsDumpedEvent(), ManifestsDumpedEvent::NAME);
    }

    public function dumpApps(string $directoryPath, string ...$appAliases): void
    {
        $directoryPath = self::validDirectoryPath($directoryPath);

        foreach ($appAliases as $alias) {
            $dir = $directoryPath.DIRECTORY_SEPARATOR.$alias;
            $this->dumper->dump($alias, $dir);
        }

        $this->dispatcher->dispatch(new ManifestsDumpedEvent(), ManifestsDumpedEvent::NAME);
    }


    public function processAll(): void
    {
        foreach ($this->registry->aliases() as $alias) {
            $this->processor->process($alias);
        }

        $this->dispatcher->dispatch(new ManifestsProcessedEvent(), ManifestsProcessedEvent::NAME);
    }

    public function processApps(string ...$appAliases): void
    {
        foreach ($appAliases as $alias) {
            $this->processor->process($alias);
        }

        $this->dispatcher->dispatch(new ManifestsProcessedEvent(), ManifestsProcessedEvent::NAME);
    }

    /**
     * @return string[] Map of rendered manifests in some format (YAML, JSON, or other - it depends on $renderer argument of constructor). Keys are file names
     */
    public function renderAll(): array
    {
        $rendered = [];
        foreach ($this->registry->aliases() as $alias) {
            $app = $this->registry->get($alias);
            foreach ($app->manifestFiles() as $file) {
                $fileName = sprintf(
                    '%s/%s%s',
                    $alias,
                    $file->fileNameWithoutExtension(),
                    $this->renderer->fileExtension()
                );
                $rendered[$fileName] = $this->renderer->render($file->resource());
            }
        }
        $this->dispatcher->dispatch(new ManifestsRenderedEvent($rendered), ManifestsRenderedEvent::NAME);

        return $rendered;
    }

    /**
     * @return string[] string[] Map of rendered manifests in some format (YAML, JSON, or other - it depends on $renderer argument of constructor). Keys are file names
     */
    public function renderApps(string ...$appAliases): array
    {
        $rendered = [];
        foreach ($appAliases as $alias) {
            $app = $this->registry->get($alias);
            foreach ($app->manifestFiles() as $file) {
                $fileName = sprintf(
                    '%s/%s%s',
                    $alias,
                    $file->fileNameWithoutExtension(),
                    $this->renderer->fileExtension()
                );
                $rendered[$fileName] = $this->renderer->render($file->resource());
            }
        }

        $this->dispatcher->dispatch(new ManifestsRenderedEvent($rendered), ManifestsRenderedEvent::NAME);

        return $rendered;
    }

    private static function validDirectoryPath(string $directoryPath): string
    {
        $realpath = realpath($directoryPath);
        $realpath || throw new InvalidArgumentException(sprintf('Directory "%s" does not exist', $directoryPath));

        return $realpath;
    }
}
