<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use K8Schema\APIResourceInterface;
use Klass8s\Core\GenericApiResource;

abstract class AbstractSelfMakingManifest extends AbstractManifest implements SelfMakingManifestInterface
{
    abstract public function spec(): array|\JsonSerializable;
    abstract public static function apiVersion(): string;
    abstract public static function kind(): string;

    public function make(): ApiResourceInterface
    {
        $spec = $this->spec();
        if ($spec instanceof \JsonSerializable) {
            $encoded = json_encode($spec);
            if (false === $encoded) {
                throw new \RuntimeException(sprintf('Failed to encode spec to JSON: %s. Spec: "%s"', json_last_error_msg(), var_export($spec, true)));
            }
            $spec = json_decode($encoded, true);
        }

        return new GenericApiResource(static::apiVersion(), static::kind(), $spec);
    }
}
