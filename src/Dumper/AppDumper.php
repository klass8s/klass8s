<?php

declare(strict_types=1);

namespace Klass8s\Dumper;

use Klass8s\Registry\AppRegistry;
use Klass8s\Renderer\RendererInterface;

final readonly class AppDumper
{
    public function __construct(
        private RendererInterface $renderer,
        private AppRegistry $appRegistry,
    ) {
    }

    public function dump(string $appAlias, string $appDir): void
    {
        @mkdir($appDir, 0o777, true);
        $app = $this->appRegistry->get($appAlias);
        foreach ($app->manifestFiles() as $file) {
            $rendered = $this->renderer->render($file->resource());
            $filePath = $appDir.DIRECTORY_SEPARATOR.$file->fileNameWithoutExtension().$this->renderer->fileExtension();
            file_put_contents($filePath, $rendered);
        }
    }
}
