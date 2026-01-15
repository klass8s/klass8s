<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\Output\OutputDirectory;

final readonly class ManifestsProcessedEvent
{
    public const string NAME = 'klass8s.manifests.processed';

    public function __construct(public OutputDirectory $outputDir)
    {
    }
}
