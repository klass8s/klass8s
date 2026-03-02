<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Gateway\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;

final readonly class ListenersConfigurator
{
    public function __construct(
        private GenericList $data,
        private AppInterface $app,
    ) {
    }

    public function add(string $name): ListenerConfigurator
    {
        $listenerData = new \ArrayObject();
        $listenerData['name'] = $name;
        $this->data->add($listenerData);

        return new ListenerConfigurator($listenerData, $this->app);
    }
}
