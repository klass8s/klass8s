<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\Event\ManifestGeneratedEvent;
use Klass8s\Output\OutputDirectory;
use Klass8s\Output\RenderableFile;
use Klass8s\ResourceMaker\AbstractResourceMaker;
use Klass8s\ResourceMaker\ResourceMakerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class ManifestProcessor
{
    /**
     * @var ResourceMakerInterface[]
     */
    private array $makers = [];

    /**
     * @param ResourceMakerInterface[] $makers
     */
    public function __construct(private readonly EventDispatcherInterface $dispatcher, iterable $makers)
    {

        foreach ($makers as $maker) {
            if ($maker instanceof AbstractResourceMaker) {
                $maker->setEventDispatcher($this->dispatcher);
            }
            $this->makers[] = $maker;
        }
    }

    public function process(ManifestInterface $manifest, OutputDirectory $outputDir): void
    {
        $manifestConfig = $manifest->app->manifestConfig($manifest);
        $manifest->setConfig($manifestConfig);

        foreach ($this->makers as $maker) {
            if (!$maker->supports($manifest)) {
                continue;
            }

            $resource = $maker->make($manifest);
            $event = new ManifestGeneratedEvent($manifest, $resource);
            $this->dispatcher->dispatch($event, ManifestGeneratedEvent::NAME);

            if (!$event->preventProcessing) {
                $file = new RenderableFile($manifest->fileName, $resource);
                $outputDir->addFile($file);
            }

            return;
        }

        throw new \RuntimeException(sprintf('No resource maker found for manifest "%s".', $manifest::class));
    }
}
