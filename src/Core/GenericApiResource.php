<?php

declare(strict_types=1);

namespace Klass8s\Core;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;

class GenericApiResource implements APIResourceInterface
{
    private ObjectMeta $metadata;

    public function __construct(
        private readonly string $apiVersion,
        private readonly string $kind,
        private readonly array $spec = []
    ) {
        $this->metadata = new ObjectMeta();
    }

    public function metadata(): ObjectMeta
    {
        return $this->metadata;
    }

    public function jsonSerialize(): array|\JsonSerializable
    {
        return [
            'apiVersion' => $this->apiVersion,
            'kind' => $this->kind,
            'metadata' => $this->metadata,
            'spec' => $this->spec,
        ];
    }
}
