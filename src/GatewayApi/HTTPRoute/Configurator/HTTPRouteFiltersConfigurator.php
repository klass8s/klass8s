<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

final class HTTPRouteFiltersConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(): HTTPRouteFilterConfigurator
    {
        $filterData = new \ArrayObject();
        $this->data[] = $filterData;

        return new HTTPRouteFilterConfigurator($filterData);
    }
}
