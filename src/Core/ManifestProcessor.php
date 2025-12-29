<?php

declare(strict_types=1);

namespace Klass8s\Core;

use Klass8s\App\AppInterface;
use Klass8s\Event\ManifestGeneratedEvent;
use Klass8s\ResourceMaker\AbstractResourceMaker;
use Klass8s\ResourceMaker\ResourceMakerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class ManifestProcessor
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

    public function process(ManifestInterface $manifest, AppInterface $app): void
    {
        $manifest->setConfig($app->manifestConfig($manifest::shortName()));
        foreach ($this->makers as $maker) {
            if ($maker->supports($manifest, $app)) {
                $resource = $maker->make($manifest, $app);
                $event = new ManifestGeneratedEvent($manifest, $resource, $app);
                $this->dispatcher->dispatch($event, ManifestGeneratedEvent::NAME);

                if (!$event->preventProcessing) {
                    $app->addManifestFile($manifest->fileNameWithoutExtension(), $resource);
                }

                return;
            }
        }
    }
}
