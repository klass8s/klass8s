<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod;

use K8Schema\Api\Core\V1\PodTemplateSpec;
use Klass8s\App\AppInterface;
use Klass8s\Core\MetadataConfigurator;
use Klass8s\Event\PodTemplateSpecGeneratedEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final readonly class PodTemplateSpecProcessor
{
    public function __construct(
        private PodSpecProcessor $specProcessor,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function process(PodTemplateSpecInterface $builder, PodTemplateSpec $templateSpec, AppInterface $app): void
    {
        $meta = $templateSpec->metadata();
        $metaConfigurator = new MetadataConfigurator($meta->labels(), $meta->annotations());
        $builder->metadata($metaConfigurator);

        $this->specProcessor->process($builder, $templateSpec->spec(), $app);

        $this->dispatcher->dispatch(new PodTemplateSpecGeneratedEvent($templateSpec, $builder), PodTemplateSpecGeneratedEvent::NAME);
    }
}
