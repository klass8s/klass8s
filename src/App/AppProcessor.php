<?php

declare(strict_types=1);

namespace Klass8s\App;

use Klass8s\Manifest\ManifestProcessor;
use Klass8s\Output\OutputDirectory;
use Klass8s\Registry\ManifestRegistry;
use Klass8s\Renderer\RendererInterface;

final readonly class AppProcessor
{
    public function __construct(
        private ManifestRegistry $manifestRegistry,
        private ManifestProcessor $manifestProcessor,
        private RendererInterface $renderer,
    ) {
    }

    public function process(string $appAlias): OutputDirectory
    {
        $outputDir = new OutputDirectory($appAlias, $this->renderer);

        foreach ($this->manifestRegistry->query($appAlias)->execute() as $manifest) {
            $this->manifestProcessor->process($manifest, $outputDir);
        }

        return $outputDir;
    }
}
