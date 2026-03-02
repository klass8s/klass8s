<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GatewayClass;

final readonly class ParametersRef
{
    public function __construct(
        public string $group,
        public string $kind,
        public string $name,
        public ?string $namespace = null,
    ) {
    }

    /**
     * @return array{group: string, kind: string, name: string, namespace?: string}
     */
    public function toArray(): array
    {
        $data = [
            'group' => $this->group,
            'kind' => $this->kind,
            'name' => $this->name,
        ];

        if (null !== $this->namespace) {
            $data['namespace'] = $this->namespace;
        }

        return $data;
    }
}
