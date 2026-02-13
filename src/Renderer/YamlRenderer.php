<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

use Symfony\Component\Yaml\Yaml;

class YamlRenderer extends AbstractRenderer
{
    public function render(array|object $resource): string
    {
        $data = $this->withoutEmptyValues($resource);
        //echo var_export($data), PHP_EOL; die();

        return Yaml::dump(
            $data,
            5,
            2,
            Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK | Yaml::DUMP_OBJECT_AS_MAP | Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE,
        );
    }

    public function fileExtension(): string
    {
        return '.yaml';
    }
}
