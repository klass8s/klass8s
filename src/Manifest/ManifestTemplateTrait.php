<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

trait ManifestTemplateTrait
{
    protected bool $manifestNameSet = false;

    public protected(set) string $manifestName {
        get {
            if (!$this->manifestNameSet) {
                throw new \RuntimeException('Manifest name is not set yet.');
            }

            return $this->manifestName;
        }
        set {
            if ($this->manifestNameSet) {
                throw new \RuntimeException('Manifest name is already set and cannot be changed.');
            }

            $this->manifestName = $value;
            $this->manifestNameSet = true;
        }
    }

    public static function manifestName(): string
    {
        throw new \BadMethodCallException(sprintf('Class "%s" is a manifest template, therefore the name of the manifest cannot be retrieved statically. Use `$manifestName` property on the instance of the class to get instance\'s name.', static::class));
    }
}
