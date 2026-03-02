<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\App\AppInterface;

final class ListenersConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function add(string $name): ListenerConfigurator
    {
        $listenerData = new \ArrayObject();
        $listenerData['name'] = $name;
        $this->data[] = $listenerData;

        return new ListenerConfigurator($listenerData, $this->app);
    }
}
