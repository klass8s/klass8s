<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Apps\V1\Deployment;
use K8Schema\APIResourceInterface;
use Klass8s\Core\Deployment\DeploymentInterface;

final readonly class DeploymentGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.deployment';

    public function __construct(private DeploymentInterface $manifest, private Deployment $deployment)
    {
    }

    public function manifest(): DeploymentInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->deployment;
    }

    public function deployment(): Deployment
    {
        return $this->deployment;
    }
}
