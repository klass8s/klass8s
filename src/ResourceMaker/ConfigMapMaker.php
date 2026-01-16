<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Core\V1\ConfigMap;
use Klass8s\Core\ConfigMap\ConfigMapInterface;
use Klass8s\Event\ConfigMapGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\ResourceMaker\Traits\PrefixMapKeysTrait;
use Klass8s\Value\StringMapProxy;

final class ConfigMapMaker extends AbstractResourceMaker
{
    use PrefixMapKeysTrait;

    protected function makeResource(ManifestInterface|ConfigMapInterface $manifest): ConfigMap
    {
        $configMap = new ConfigMap();

        $app = $manifest->app;
        $app::configureMeta($manifest, $configMap);
        $data = StringMapProxy::make($configMap->data());
        $manifest->data($data);
        $binaryData = $configMap->binaryData();
        $manifest->binaryData($binaryData);

        if ($prefix = $manifest->keysPrefix()) {
            $this->prefixMapKeys($prefix, $data);
            $this->prefixMapKeys($prefix, $binaryData);
        }

        $manifest->configureConfigMap($configMap);

        $this->dispatcher->dispatch(new ConfigMapGeneratedEvent($manifest, $configMap, $app), ConfigMapGeneratedEvent::NAME);

        return $configMap;
    }

    protected function supportedClass(): string
    {
        return ConfigMapInterface::class;
    }
}
