<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

final class HTTPBackendRefsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(): HTTPBackendRefConfigurator
    {
        $refData = new \ArrayObject();
        $this->data[] = $refData;

        return new HTTPBackendRefConfigurator($refData);
    }
}
