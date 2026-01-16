<?php

declare(strict_types=1);

namespace Klass8s\Core;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;

readonly class GenericApiResource implements APIResourceInterface
{
    private ObjectMeta $metadata;

    /**
     * @param array<string, mixed> $spec
     */
    public function __construct(private string $apiVersion, private string $kind, private array $spec = [])
    {
        $this->metadata = new ObjectMeta();
    }

    public function metadata(): ObjectMeta
    {
        return $this->metadata;
    }

    public function jsonSerialize(): array
    {
        return [
            'apiVersion' => $this->apiVersion,
            'kind' => $this->kind,
            'metadata' => $this->metadata,
            'spec' => $this->spec,
        ];
    }
}
