<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

use K8Schema\Api\Apps\V1\Deployment;
use Klass8s\App\AppInterface;
use Klass8s\Core\Deployment\DeploymentInterface;

/**
 * Everything a {@see ScalingTriggerResolverInterface} may need to turn a trigger attribute
 * into a concrete KEDA trigger: the workload manifest the attribute was found on, its
 * generated Deployment resource, the owning app and the current environment.
 */
final readonly class AutoscaleContext
{
    public function __construct(
        public DeploymentInterface $manifest,
        public Deployment $deployment,
        public AppInterface $app,
        public string $env,
    ) {
    }
}
