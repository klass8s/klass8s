<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\App\AbstractApp;
use Klass8s\App\AppInterface;
use Klass8s\Config\ConfigAwareTrait;
use Klass8s\Core\DynamicNameAwareInterface;
use Klass8s\Core\MetadataConfigurator;
use Klass8s\Util\StringUtil;
use Proximity\ProxyInterface;

abstract class AbstractManifest implements ManifestInterface
{
    use ConfigAwareTrait;

    public protected(set) AppInterface|AbstractApp $app;

    public string $manifestInstanceName {
        get => match (true) {
            $this instanceof FullNameAwareInterface => $this->manifestFullName(),
            $this instanceof ManifestTemplateInterface => $this->manifestName,
            default => $this::manifestName(),
        };
    }

    public string $resourceName {
        get {
            if ($this instanceof FullNameAwareInterface) {
                return $this->manifestFullName();
            }

            return ManifestNameResolver::resourceNameFromManifestNameAndPrefix($this->manifestInstanceName, $this->app->alias());
        }
    }

    public string $fileName {
        get {
            if (!$this->manifestInstanceName) {
                throw new \LogicException(sprintf('Manifest instance name cannot be empty in class "%s".', $this::className()));
            }

            return $this->manifestInstanceName.'.'.StringUtil::asDNSSubdomain($this::kind());
        }
    }

    public function fileNameWithoutExtension(): string
    {
        $name = match (true) {
            $this instanceof FullNameAwareInterface => $this->manifestFullName(),
            $this instanceof DynamicNameAwareInterface => $this->name(),
            default => $this::manifestName(),
        };

        return $name.'.'.StringUtil::asDNSSubdomain(static::kind());
    }

    public function metadata(MetadataConfigurator $meta): void
    {
    }

    public function setApp(AppInterface $app): void
    {
        $this->app = $app;
    }

    public static function className(): string
    {
        $class = new \ReflectionClass(static::class);
        if ($class->implementsInterface(ProxyInterface::class)) {
            $class = $class->getParentClass();
        }

        return $class->getName();
    }
}
