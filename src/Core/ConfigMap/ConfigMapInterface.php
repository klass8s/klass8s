<?php

declare(strict_types=1);

namespace Klass8s\Core\ConfigMap;

use K8Schema\Api\Core\V1\ConfigMap;
use K8Schema\Collection\StringMap;
use Klass8s\Core\ManifestInterface;

interface ConfigMapInterface extends ManifestInterface
{
    public function data(StringMap $data): void;
    public function binaryData(StringMap $binaryData): void;
    public function keysPrefix(): string;
    public function configureConfigMap(ConfigMap $configMap): void;
}
