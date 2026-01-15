<?php

declare(strict_types=1);

namespace Klass8s\Core\ServiceAccount\Configurator;

use K8Schema\Api\Core\V1\ObjectReference;
use K8Schema\Api\Core\V1\ObjectReferenceList;
use Klass8s\Core\Secret\SecretInterface;
use Klass8s\Manifest\Reference\ManifestClassReference;
use Klass8s\Manifest\Reference\ManifestClassReferenceConverter;

readonly class SecretsConfigurator
{
    public function __construct(
        private ManifestClassReferenceConverter $referencesService,
        private ObjectReferenceList $secrets
    ) {
    }

    public function addFromManifestReference(ManifestClassReference $secretReference): static
    {
        $secretClass = $secretReference->className();
        if (!is_subclass_of($secretClass, SecretInterface::class, true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'SecretsConfigurator accepts references to secrets, but class "%s" in provided reference does not implement SecretInterface',
                    $secretClass
                )
            );
        }

        $this->secrets->add($this->referencesService->toObjectReference($secretReference));

        return $this;
    }

    public function addFromObjectReference(ObjectReference $secretReference): static
    {
        $this->secrets->add($secretReference);

        return $this;
    }
}
