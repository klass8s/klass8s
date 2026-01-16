<?php

declare(strict_types=1);

namespace Klass8s\Output;

use K8Schema\APIResourceInterface;
use Klass8s\Renderer\RendererInterface;

final readonly class RenderableFile
{
    public function __construct(public string $nameWithoutExtension, public APIResourceInterface $resource)
    {
    }

    public function render(RendererInterface $renderer): string
    {
        return $renderer->render($this->resource);
    }
}
