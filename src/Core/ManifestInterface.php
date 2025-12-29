<?php

declare(strict_types=1);

namespace Klass8s\Core;

use Klass8s\Config\ConfigAwareInterface;

interface ManifestInterface extends AppAwareInterface, ConfigAwareInterface, MetadataAwareInterface, ProxyableInterface
{
    public static function apiVersion(): string;
    public static function kind(): string;
    public static function shortName(): string;
    public function fileNameWithoutExtension(): string;
}
