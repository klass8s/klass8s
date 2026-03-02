<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

final class ListenersConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(string $name): ListenerConfigurator
    {
        $listenerData = new \ArrayObject();
        $listenerData['name'] = $name;
        $this->data[] = $listenerData;

        return new ListenerConfigurator($listenerData);
    }
}
