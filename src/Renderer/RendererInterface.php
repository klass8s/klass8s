<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

interface RendererInterface
{
    public function render(\JsonSerializable|array $resource): string;
    public function fileExtension(): string;
}
