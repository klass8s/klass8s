<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume\Builder;

use K8Schema\Api\Core\V1\KeyToPath;
use K8Schema\Api\Core\V1\SecretVolumeSource;
use K8Schema\Api\Core\V1\Volume;

class SecretVolumeBuilder extends AbstractVolumeBuilder
{
    private SecretVolumeSource $source;

    public function __construct(private readonly string $secretName)
    {
    }

    public function init(Volume $volume): void
    {
        $this->source = $volume->secret();
        $this->source->setSecretName($this->secretName);
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
