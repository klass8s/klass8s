<?php

declare(strict_types=1);

namespace Klass8s\Core\ConfigMap;

use K8Schema\Api\Core\V1\ConfigMap;
use K8Schema\Collection\StringMap;
use Klass8s\Core\AbstractManifest;

abstract class AbstractConfigMap extends AbstractManifest implements ConfigMapInterface
{
    public function data(StringMap $data): void
    {
    }

    public function binaryData(StringMap $binaryData): void
    {
    }

    public function keysPrefix(): string
    {
        return '';
    }

    public function configureConfigMap(ConfigMap $configMap): void
    {
    }

    final public static function kind(): string
    {
        return ConfigMap::KIND;
    }

    final public static function apiVersion(): string
    {
        return ConfigMap::API_VERSION;
    }
}
