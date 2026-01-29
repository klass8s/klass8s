<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Reference;

use K8Schema\Api\Autoscaling\V2\CrossVersionObjectReference;
use Klass8s\Registry\AppRegistry;

final readonly class VersionedManifestClassReferenceConverter
{
    public function __construct(private AppRegistry $appRegistry)
    {
    }

    public function toCrossVersionObjectReference(VersionedManifestClassReference $reference): CrossVersionObjectReference
    {
        $app = $this->appRegistry->get($reference->appAlias);
        $name = $app->resourceNameFromManifestClass($reference->className);

        $class = new \ReflectionClass($reference->className);
        $kind = $class->getMethod('kind')->invoke(null);
        $apiVersion = $reference->apiVersion ?: $class->getMethod('apiVersion')->invoke(null);

        $ref = new CrossVersionObjectReference($kind, $name);
        $ref->setApiVersion($apiVersion);

        return $ref;
    }
}
