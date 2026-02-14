<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends AbstractRenderer
{
    public function render(array|object $resource): string
    {
        $data = $this->withoutEmptyValues($resource);

        return Yaml::dump(
            $data,
            7,
            2,
            Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE,
        );
    }

    public function fileExtension(): string
    {
        return '.yaml';
    }
}
