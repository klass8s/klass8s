<?php

declare(strict_types=1);

namespace Klass8s\Util;

use K8Schema\Api\Autoscaling\V2\CrossVersionObjectReference;
use Klass8s\Core\VersionedManifestReference;
use Klass8s\Registry\AppRegistry;

final readonly class VersionedManifestReferencesService
{
    public function __construct(private AppRegistry $appRegistry)
    {
    }

    public function toCrossVersionObjectReference(VersionedManifestReference $reference): CrossVersionObjectReference
    {
        $app = $this->appRegistry->get($reference->appAlias);
        $name = $app->namesHelper()->byManifestClass($reference->className);

        $class = new \ReflectionClass($reference->className);
        $kind = $class->getMethod('kind')->invoke(null);
        $apiVersion = $reference->apiVersion ?: $class->getMethod('apiVersion')->invoke(null);

        $ref = new CrossVersionObjectReference($kind, $name);
        $ref->setApiVersion($apiVersion);

        return $ref;
    }
}
