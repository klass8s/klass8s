<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

interface MetadataAwareInterface
{
    public function metadata(MetadataConfigurator $meta): void;
}
