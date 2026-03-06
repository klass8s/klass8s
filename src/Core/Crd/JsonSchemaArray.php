<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\JSONSchemaProps;

/**
 * Wraps a raw OpenAPI v3 schema array as a JSONSchemaProps implementation.
 */
readonly class JsonSchemaArray implements JSONSchemaProps, \JsonSerializable
{
    /**
     * @param array<string, mixed> $schema
     */
    public function __construct(
        private array $schema,
    ) {
    }

    public function jsonSerialize(): mixed
    {
        return $this->schema;
    }
}
