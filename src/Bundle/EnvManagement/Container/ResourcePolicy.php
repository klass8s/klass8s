<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Container;

use K8Schema\Api\Core\V1\ResourceRequirements;
use Klass8s\Core\Container\Resources\ResourcesConfigurator;

readonly class ResourcePolicy
{
    public ResourcesConfigurator $defaults;

    public function __construct()
    {
        $this->defaults = new ResourcesConfigurator(new ResourceRequirements());
    }
}
