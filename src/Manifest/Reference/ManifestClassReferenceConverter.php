<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

use K8Schema\Api\Core\V1\LocalObjectReference;
use K8Schema\Api\Core\V1\ObjectReference;
use K8Schema\Api\Core\V1\TypedLocalObjectReference;
use Klass8s\Registry\AppRegistry;

final readonly class ManifestClassReferenceConverter
{
    public function __construct(private AppRegistry $appRegistry)
    {
    }

    public function toTypedLocalObjectReference(ManifestClassReference $manifestReference): TypedLocalObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $class = new \ReflectionClass($manifestReference->className);
        $kind = $class->getMethod('kind')->invoke(null);

        $objectReference = new TypedLocalObjectReference($kind, $name);
        if ($apiGroup = $manifestReference->apiGroup()) {
            $objectReference->setApiGroup($apiGroup);
        }

        return $objectReference;
    }

    public function toLocalObjectReference(ManifestClassReference $manifestReference): LocalObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $objectReference = new LocalObjectReference();
        $objectReference->setName($name);

        return $objectReference;
    }

    public function toObjectReference(ManifestClassReference $manifestReference): ObjectReference
    {
        $name = $this->toResourceName($manifestReference);

        $class = new \ReflectionClass($manifestReference->className);
        $kind = $class->getMethod('kind')->invoke(null);

        $objectReference = new ObjectReference();
        $objectReference
            ->setName($name)
            ->setKind($kind)
        ;

        return $objectReference;
    }

    public function toResourceName(ManifestClassReference $manifestReference): string
    {
        $app = $this->appRegistry->get($manifestReference->appAlias);

        return $app->resourceNameFromManifestClass($manifestReference->className);
    }
}
