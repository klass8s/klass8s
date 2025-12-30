<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\API\ScaledObject;

use K8Schema\Api\Apps\V1\Deployment;

readonly class ScaleTargetReference implements \JsonSerializable
{
    public function __construct(
        public string $name,
        public string $apiVersion = Deployment::API_VERSION,
        public string $kind = Deployment::KIND,
        public ?string $envSourceContainerName = null,
    ) {
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'apiVersion' => $this->apiVersion,
            'kind' => $this->kind,
            'envSourceContainerName' => $this->envSourceContainerName,
        ];
    }
}
