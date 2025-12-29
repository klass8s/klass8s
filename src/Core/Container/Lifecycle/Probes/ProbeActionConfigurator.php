<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Lifecycle\Probes;

use K8Schema\Api\Core\V1\Probe;
use Klass8s\Core\Container\Lifecycle\Action\ActionConfiguratorTrait;

class ProbeActionConfigurator
{
    use ActionConfiguratorTrait;

    private Probe $handler;

    public function __construct(Probe $handler)
    {
        $this->handler = $handler;
    }
}
