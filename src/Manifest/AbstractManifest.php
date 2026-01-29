<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\App\AbstractApp;
use Klass8s\App\AppInterface;
use Klass8s\Config\ConfigAwareTrait;
use Klass8s\Util\StringUtil;
use Proximity\ProxyInterface;

abstract class AbstractManifest implements ManifestInterface
{
    use ConfigAwareTrait;

    protected(set) AppInterface|AbstractApp $app;

    public string $computedShortName {
        get => match (true) {
            $this instanceof FullNameAwareInterface => throw new \LogicException(sprintf('$computedShortName must not be accessed on a full-name aware manifest "%s".', $this::className())),
            $this instanceof ManifestTemplateInterface => $this->dynamicShortName,
            default => $this::shortName(),
        };
    }

    public string $computedFullName {
        get {
            if ($this instanceof FullNameAwareInterface) {
                return $this->fullName();
            }

            return $this->app->resourceNameFromShortName($this->computedShortName);
        }
    }

    public string $fileName {
        get {
            if ($this instanceof FullNameAwareInterface) {
                $name = $this->fullName();
            } elseif (!$this->computedShortName) {
                throw new \LogicException(sprintf('Short name cannot be empty in class "%s".', $this::className()));
            } else {
                $name = $this->computedShortName;
            }

            return $name.'.'.StringUtil::asDNSSubdomain($this::kind());
        }
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

        return $class->name;
    }
}
