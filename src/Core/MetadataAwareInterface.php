<?php

declare(strict_types=1);

namespace Klass8s\Core;

interface MetadataAwareInterface
{
    public function metadata(MetadataConfigurator $meta): void;
}
