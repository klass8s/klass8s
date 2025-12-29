<?php

declare(strict_types=1);

namespace Klass8s\Event;

final readonly class ManifestsRenderedEvent
{
    public const string NAME = 'klass8s.manifests.rendered';

    public function __construct(public array $renderedManifests)
    {
    }
}
