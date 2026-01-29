<?php

declare(strict_types=1);

namespace Klass8s\App;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Config\ConfigurableInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\MetadataAwareInterface;

interface AppInterface extends ConfigurableInterface
{
    public static function name(): string;

    /**
     * @return string The alias with which app was registered in AppRegistry
     */
    public function alias(): string;

    /**
     * @param class-string<ManifestInterface> $manifestClass
     * @param class-string<ManifestInterface>|null $classToValidateAgainst If set, $manifestClass will be ensured to be a subclass of this
     */
    public function resourceNameFromManifestClass(string $manifestClass, ?string $classToValidateAgainst = null): string;
    public function resourceNameFromShortName(string $shortName): string;
    public function manifestConfig(ManifestInterface $manifest): array;
    public function getManifestByClass(string $manifestClass): ?ManifestInterface;
    public function ownsManifestClass(string $manifestClass): bool;
    public static function configureMeta(MetadataAwareInterface $metadataAware, APIResourceInterface $apiObject): ObjectMeta;
}
