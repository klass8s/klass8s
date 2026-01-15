<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\ImagePullSecrets;

use K8Schema\Api\Core\V1\LocalObjectReference;
use K8Schema\Api\Core\V1\LocalObjectReferenceList;
use Klass8s\App\AppInterface;
use Klass8s\Core\Secret\SecretInterface;

final readonly class ImagePullSecretsConfigurator
{
    public function __construct(
        private LocalObjectReferenceList $secrets,
        private AppInterface $app,
    ) {
    }

    /**
     * @param class-string<SecretInterface> $secretClass
     */
    public function addSecretClass(string $secretClass): self
    {
        $secretName = $this->app->resourceNameByManifestClass($secretClass, SecretInterface::class);

        return $this->addSecret($secretName);
    }

    public function addSecret(string $secretName): self
    {
        $ref = new LocalObjectReference();
        $ref->setName($secretName);
        $this->secrets->add($ref);

        return $this;
    }
}
