<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Core\V1\ConfigMap;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\ConfigMap\ConfigMapInterface;

readonly class ConfigMapGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.config_map';

    public function __construct(private ConfigMapInterface $manifest, private ConfigMap $configMap, private AppInterface $app)
    {
    }

    public function manifest(): ConfigMapInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->configMap;
    }

    public function configMap(): ConfigMap
    {
        return $this->configMap;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
