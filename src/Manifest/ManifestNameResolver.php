<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

final readonly class ManifestNameResolver
{
    public const int MAX_NAME_LENGTH = 52;

    private function __construct()
    {
    }

    /**
     * @param class-string<ManifestInterface> $className
     */
    public static function manifestNameFromClass(string $className): string
    {
        self::ensureStaticallyNamedManifestClass($className);

        return $className::shortName();
    }

    /**
     * @param class-string<ManifestInterface> $className
     */
    public static function resourceNameFromManifestClass(string $className, string $namePrefix = ''): string
    {
        self::ensureStaticallyNamedManifestClass($className);

        return self::resourceNameFromShortNameAndPrefix($className::shortName(), $namePrefix);
    }

    public static function resourceNameFromShortNameAndPrefix(string $manifestName, string $namePrefix): string
    {
        if ($namePrefix) {
            $namePrefix .= '-';
        }
        $name = $namePrefix.$manifestName;

        self::ensureValidNameLength($name);

        return $name;
    }

    /**
     * @param class-string<ManifestInterface> $className
     */
    private static function ensureStaticallyNamedManifestClass(string $className): void
    {
        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist', $className));
        }

        if (!is_a($className, ManifestInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf('$className must be a name of class that implements %s, given class "%s" does not.', ManifestInterface::class, $className));
        }

        if (is_a($className, FullNameAwareInterface::class, true)) {
            throw new \LogicException(sprintf('Class "%s" is full name aware (see "%s") and its instances are named dynamically, - therefore manifest name cannot be retrieved statically.', $className, FullNameAwareInterface::class));
        }

        if (is_a($className, ManifestTemplateInterface::class, true)) {
            throw new \LogicException(sprintf('Class "%s" is a manifest template, therefore the name of the manifest cannot be retrieved statically.', $className));
        }
    }

    private static function ensureValidNameLength(string $name): void
    {
        $nameLength = strlen($name);
        if ($nameLength > self::MAX_NAME_LENGTH) {
            throw new \InvalidArgumentException(sprintf('Resource name in Kubernetes is limited to %d characters, but name "%s" is %d characters long', self::MAX_NAME_LENGTH, $name, $nameLength));
        }
    }
}
