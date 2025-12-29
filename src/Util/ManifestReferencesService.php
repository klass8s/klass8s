<?php

declare(strict_types=1);

namespace Klass8s\Util;

use K8Schema\Api\Core\V1\LocalObjectReference;
use K8Schema\Api\Core\V1\ObjectReference;
use K8Schema\Api\Core\V1\TypedLocalObjectReference;
use Klass8s\Core\ManifestReference;
use Klass8s\Registry\AppRegistry;

final readonly class ManifestReferencesService
{
    public function __construct(private AppRegistry $appRegistry)
    {
    }

    public function toTypedLocalObjectReference(ManifestReference $manifestReference): TypedLocalObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $class = new \ReflectionClass($manifestReference->className());
        $kind = $class->getMethod('kind')->invoke(null);

        $objectReference = new TypedLocalObjectReference($kind, $name);
        if ($apiGroup = $manifestReference->apiGroup()) {
            $objectReference->setApiGroup($apiGroup);
        }

        return $objectReference;
    }

    public function toLocalObjectReference(ManifestReference $manifestReference): LocalObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $objectReference = new LocalObjectReference();
        $objectReference->setName($name);

        return $objectReference;
    }

    public function toObjectReference(ManifestReference $manifestReference): ObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $class = new \ReflectionClass($manifestReference->className());
        $kind = $class->getMethod('kind')->invoke(null);

        $objectReference = new ObjectReference();
        $objectReference
            ->setName($name)
            ->setKind($kind)
        ;

        return $objectReference;
    }

    public function toResourceName(ManifestReference $manifestReference): string
    {
        $app = $this->appRegistry->get($manifestReference->appAlias());

        return $app->namesHelper()->byManifestClass($manifestReference->className());
    }
}
