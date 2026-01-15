<?php

declare(strict_types=1);

namespace Klass8s\Core\ServiceAccount;

use K8Schema\Api\Core\V1\ServiceAccount;
use Klass8s\Core\ServiceAccount\Configurator\ImagePullSecretsConfigurator;
use Klass8s\Core\ServiceAccount\Configurator\SecretsConfigurator;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractServiceAccount extends AbstractManifest implements ServiceAccountInterface
{
    public function automountServiceAccountToken(): bool
    {
        return false;
    }

    public function imagePullSecrets(ImagePullSecretsConfigurator $imagePullSecrets): void
    {
    }

    public function secrets(SecretsConfigurator $secrets): void
    {
    }

    public function configureServiceAccount(ServiceAccount $serviceAccount): void
    {
    }

    final public static function kind(): string
    {
        return ServiceAccount::KIND;
    }

    final public static function apiVersion(): string
    {
        return ServiceAccount::API_VERSION;
    }
}
