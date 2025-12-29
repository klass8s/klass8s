<?php

declare(strict_types=1);

namespace Klass8s\Core;

use K8Schema\Collection\StringMap;

class MetadataConfigurator
{
    private StringMap $labels;
    private StringMap $annotations;

    public function __construct(StringMap $labels, StringMap $annotations)
    {
        $this->labels = $labels;
        $this->annotations = $annotations;
    }

    public function labels(): StringMap
    {
        return $this->labels;
    }

    public function annotations(): StringMap
    {
        return $this->annotations;
    }
}
