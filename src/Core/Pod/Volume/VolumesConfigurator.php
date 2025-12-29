<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Volume;

use K8Schema\Api\Core\V1\Volume;
use K8Schema\Api\Core\V1\VolumeList;
use Klass8s\App\AppInterface;
use Klass8s\Core\Pod\Volume\Builder\ConfigMapVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\DownwardAPIVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\EmptyDirVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\HostPathVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\PVCVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\SecretVolumeBuilder;
use Klass8s\Core\Pod\Volume\Builder\VolumeBuilderInterface;
use Klass8s\Registry\AppRegistry;

readonly class VolumesConfigurator
{
    public function __construct(
        private VolumeList $volumes,
        private AppInterface $app,
        private AppRegistry $registry
    ) {
    }

    public function fromConfigMap(string $volumeName, string $configMapClass): ConfigMapVolumeBuilder
    {
        $cmName = $this->app->namesHelper()->byConfigMapClass($configMapClass);

        return $this->fromConfigMapName($volumeName, $cmName);
    }

    public function fromConfigMapName(string $volumeName, string $configMapName): ConfigMapVolumeBuilder
    {
        return $this->initBuilder(new ConfigMapVolumeBuilder($configMapName), $volumeName);
    }

    public function fromEmptyDir(string $volumeName): EmptyDirVolumeBuilder
    {
        $builder = new EmptyDirVolumeBuilder();

        return $this->initBuilder($builder, $volumeName);
    }

    public function fromDownwardAPI(string $volumeName): DownwardAPIVolumeBuilder
    {
        $builder = new DownwardAPIVolumeBuilder();

        return $this->initBuilder($builder, $volumeName);
    }

    public function fromPersistentVolumeClaim(string $volumeName, string $pvcClass): PVCVolumeBuilder
    {
        $pvcName = $this->app->namesHelper()->byManifestClass($pvcClass);
        $builder = new PVCVolumeBuilder($pvcName);

        return $this->initBuilder($builder, $volumeName);
    }

    public function fromSecret(string $volumeName, string $secretClass): SecretVolumeBuilder
    {
        $secretName = $this->app->namesHelper()->bySecretClass($secretClass);

        return $this->fromSecretName($volumeName, $secretName);
    }

    public function fromSecretName(string $volumeName, string $secretName): SecretVolumeBuilder
    {
        return $this->initBuilder(new SecretVolumeBuilder($secretName), $volumeName);
    }

    public function fromHostPath(string $volumeName, string $path): HostPathVolumeBuilder
    {
        $builder = new HostPathVolumeBuilder($path);
        $this->initBuilder($builder, $volumeName);

        return $builder;
    }

    public function withExternalApp(string $appAlias): VolumesConfigurator
    {
        return new static($this->volumes, $this->registry->get($appAlias), $this->registry);
    }

    private function createVolume(string $name): Volume
    {
        $volume = new Volume($name);
        $this->volumes->add($volume);

        return $volume;
    }

    /**
     * @template T of VolumeBuilderInterface
     *
     * @param T $builder
     * @param string $volumeName
     *
     * @return T
     */
    private function initBuilder(VolumeBuilderInterface $builder, string $volumeName): VolumeBuilderInterface
    {
        $volume = $this->createVolume($volumeName);
        $builder->init($volume);

        return $builder;
    }
}
