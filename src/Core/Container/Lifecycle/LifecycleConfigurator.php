<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Lifecycle;

use K8Schema\Api\Core\V1\Lifecycle;
use Klass8s\Core\Container\Lifecycle\Action\LifecycleActionConfigurator;

class LifecycleConfigurator
{
    private Lifecycle $lifecycle;

    public function __construct(Lifecycle $lifecycle)
    {
        $this->lifecycle = $lifecycle;
    }

    public function preStop(): LifecycleActionConfigurator
    {
        return new LifecycleActionConfigurator($this->lifecycle->preStop());
    }

    public function postStart(): LifecycleActionConfigurator
    {
        return new LifecycleActionConfigurator($this->lifecycle->postStart());
    }
}
