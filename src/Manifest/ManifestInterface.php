<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\Config\ConfigAwareInterface;
use Klass8s\Proxy\ProxyableInterface;

interface ManifestInterface extends AppAwareInterface, ConfigAwareInterface, MetadataAwareInterface, ProxyableInterface
{
    /**
     * @var string The short name of the manifest instance.
     * Covers both the static short name and dynamic name in the case of `ManifestTemplateInterface`
     * @see shortName()
     * @see ManifestTemplateInterface::$dynamicShortName
     */
    public string $computedShortName { get; }

    /**
     * @var string The full name of the Kubernetes resource that will be generated from this manifest.
     */
    public string $computedFullName { get; }

    /**
     * @var string The file name (without extension like `.yaml`) generated manifest will be saved to.
     */
    public string $fileName { get; }

    public static function apiVersion(): string;
    public static function kind(): string;

    /**
     * @return string The name of the manifest, without the namespace / app prefix.
     * Example: for a manifest with the resource name `iam-main` the manifest name would be `main`.
     */
    public static function shortName(): string;

    /**
     * @return string The fully qualified class name of the manifest, unwrapped from a proxy class
     */
    public static function className(): string;
}
