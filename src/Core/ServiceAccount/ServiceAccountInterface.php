<?php

declare(strict_types=1);

namespace Klass8s\Core\ServiceAccount;

use K8Schema\Api\Core\V1\ServiceAccount;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\ServiceAccount\Configurator\ImagePullSecretsConfigurator;
use Klass8s\Core\ServiceAccount\Configurator\SecretsConfigurator;

interface ServiceAccountInterface extends ManifestInterface
{
    public function automountServiceAccountToken(): ?bool;

    public function imagePullSecrets(ImagePullSecretsConfigurator $imagePullSecrets): void;

    public function secrets(SecretsConfigurator $secrets): void;

    public function configureServiceAccount(ServiceAccount $serviceAccount): void;
}
