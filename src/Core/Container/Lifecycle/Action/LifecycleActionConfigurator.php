<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Lifecycle\Action;

use K8Schema\Api\Core\V1\LifecycleHandler;

class LifecycleActionConfigurator
{
    use ActionConfiguratorTrait;

    private LifecycleHandler $handler;

    public function __construct(LifecycleHandler $handler)
    {
        $this->handler = $handler;
    }
}
