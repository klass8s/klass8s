<?php

declare(strict_types=1);

namespace Klass8s\Core;

use Klass8s\App\AbstractApp;
use Klass8s\App\AppInterface;
use Klass8s\Config\ConfigAwareTrait;
use Klass8s\Util\Str;

abstract class AbstractManifest implements ManifestInterface
{
    use ConfigAwareTrait;

    protected AppInterface|AbstractApp $app;

    public function fileNameWithoutExtension(): string
    {
        $name = match (true) {
            $this instanceof FullNameAwareInterface => $this->fullName(),
            $this instanceof DynamicNameAwareInterface => $this->name(),
            default => $this::shortName(),
        };

        return $name.'.'.Str::asDNSSubdomain(static::kind());
    }

    public function metadata(MetadataConfigurator $meta): void
    {
    }

    public function setApp(AppInterface $app): void
    {
        $this->app = $app;
    }
}
