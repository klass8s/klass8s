<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\ImagePullSecrets;

use K8Schema\Api\Core\V1\LocalObjectReference;
use K8Schema\Api\Core\V1\LocalObjectReferenceList;
use Klass8s\App\AppInterface;

final readonly class ImagePullSecretsConfigurator
{
    public function __construct(
        private LocalObjectReferenceList $secrets,
        private AppInterface $app,
    ) {
    }

    public function addSecretByClassName(string $secretClassName): self
    {
        $secretName = $this->app->namesHelper()->bySecretClass($secretClassName);

        return $this->addSecretByName($secretName);
    }

    public function addSecretByName(string $secretName): self
    {
        $ref = new LocalObjectReference();
        $ref->setName($secretName);
        $this->secrets->add($ref);

        return $this;
    }
}
