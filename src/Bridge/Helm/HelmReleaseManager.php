<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm;

use Klass8s\Bridge\Helm\Release\HelmReleaseProcessor;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Output\OutputDirectory;
use Klass8s\Renderer\RendererInterface;

final readonly class HelmReleaseManager
{
    private const string OUTPUT_DIRECTORY_HELM = 'helm';

    public function __construct(
        private HelmReleaseProcessor $processor,
        private RendererInterface $renderer,
        private HelmReleaseRegistry $releaseRegistry,
    ) {
    }

    public function generateManifests(string ...$releaseNames): OutputDirectory
    {
        $outputDirectory = new OutputDirectory(self::OUTPUT_DIRECTORY_HELM, $this->renderer);

        foreach ($releaseNames as $releaseName) {
            $release = $this->releaseRegistry->get($releaseName);
            $releaseDir = $this->processor->process($release);
            $outputDirectory->addDirectory($releaseDir);
        }

        return $outputDirectory;
    }
}
