<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

class JsonRenderer extends AbstractRenderer
{
    public function render(array|object $resource): string
    {
        return json_encode($this->renderAsArray($resource));
    }

    public function fileExtension(): string
    {
        return '.json';
    }

    public function renderAsArray(array|object $object): array
    {
        return $this->withoutEmptyValues($object);
    }
}
