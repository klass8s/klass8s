<?php

declare(strict_types=1);

namespace Klass8s\App;

use K8Schema\APIResourceInterface;
use Klass8s\Config\ConfigurableInterface;
use Klass8s\Core\ManifestFile;
use Klass8s\Core\ManifestInterface;
use Klass8s\Helper\Metadata\MetadataHelperInterface;
use Klass8s\Helper\Names\NamesHelperInterface;

interface AppInterface extends ConfigurableInterface
{
    public static function name(): string;

    /**
     * @return string The alias with which app was registered in AppRegistry
     */
    public function alias(): string;

    public function addManifestFile(string $fileNameWithoutExtension, APIResourceInterface $resource): void;
    public function metadataHelper(): MetadataHelperInterface;
    public function namesHelper(): NamesHelperInterface;
    public function manifestNamePrefix(): string;
    public function manifestConfig(string $shortName): array;

    /**
     * @return ManifestFile[]
     */
    public function manifestFiles(): iterable;

    public function getManifest(string $manifestClass): ManifestInterface|null;

    public function ownsManifest(string $manifestClass): bool;
}
