<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\Config\ConfigAwareInterface;
use Klass8s\Core\AppAwareInterface;
use Klass8s\Core\MetadataAwareInterface;
use Klass8s\Proxy\ProxyableInterface;

interface ManifestInterface extends AppAwareInterface, ConfigAwareInterface, MetadataAwareInterface, ProxyableInterface
{
    public string $manifestInstanceName { get; }
    public string $resourceName { get; }
    public string $fileName { get; }

    public static function apiVersion(): string;
    public static function kind(): string;
    public static function manifestName(): string;

    /**
     * @return string The fully qualified class name of the manifest, unwrapped from a proxy class
     */
    public static function className(): string;

    public function fileNameWithoutExtension(): string;
}
