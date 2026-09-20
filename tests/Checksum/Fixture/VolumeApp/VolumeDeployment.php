<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\VolumeApp;

use Klass8s\Core\Container\Image\Image;
use Klass8s\Core\Container\VolumeMount\VolumeMountsConfigurator;
use Klass8s\Core\Deployment\AbstractContainerDeployment;
use Klass8s\Core\Pod\Volume\VolumesConfigurator;

/**
 * The shape the incident had: one volume from a ConfigMap this project renders, one volume from a
 * Secret it does NOT render (an External Secrets Operator target), and an init container whose env
 * comes from a third rendered ConfigMap.
 */
class VolumeDeployment extends AbstractContainerDeployment
{
    private const string VOLUME_CONFIG = 'config';
    private const string VOLUME_TLS = 'tls';

    /**
     * @param string $esoSecretName The name ESO materialises — no manifest of this project produces
     *                              it. A test parameter, so a test can change it and watch the
     *                              checksum stay put.
     */
    public function __construct(
        private readonly MigrateInitContainer $initContainer,
        private readonly string $esoSecretName,
        private readonly bool $markSecretExternal,
    ) {
    }

    public static function shortName(): string
    {
        return 'api';
    }

    public function image(): Image
    {
        return Image::fromName('nginx')->setTag('1.29');
    }

    public function initContainers(): iterable
    {
        yield $this->initContainer;
    }

    public function volumes(VolumesConfigurator $volumes): void
    {
        $volumes->fromConfigMapClass(self::VOLUME_CONFIG, MountedConfigMap::class);
        $volumes->fromSecret(self::VOLUME_TLS, $this->esoSecretName, external: $this->markSecretExternal);
    }

    public function volumeMounts(VolumeMountsConfigurator $mounts): void
    {
        $mounts->add(self::VOLUME_CONFIG, '/etc/app')->setReadOnly(true);
        $mounts->add(self::VOLUME_TLS, '/etc/tls')->setReadOnly(true);
    }
}
