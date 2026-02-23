<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\ConfigMapVolumeSource;
use K8Schema\Api\Core\V1\KeyToPath;
use K8Schema\Api\Core\V1\Volume;

class ConfigMapVolumeBuilder extends AbstractVolumeBuilder
{
    private string $configMapName;
    private ConfigMapVolumeSource $source;

    public function __construct(string $configMapName)
    {
        $this->source = new ConfigMapVolumeSource();
        $this->configMapName = $configMapName;
    }

    public function init(Volume $volume): void
    {
        $this->source->setName($this->configMapName);
        $volume->setConfigMap($this->source);
    }

    public function mapKeyToPath(string $key, string $path): self
    {
        $keyToPath = new KeyToPath($key, $path);
        $this->source->items()->add($keyToPath);

        return $this;
    }

    public function setOptional(bool $optional): self
    {
        $this->source->setOptional($optional);

        return $this;
    }

    public function setDefaultMode(int $mode): self
    {
        $this->source->setDefaultMode($mode);

        return $this;
    }
}
