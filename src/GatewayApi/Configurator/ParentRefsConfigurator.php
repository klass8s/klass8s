<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Configurator;

final class ParentRefsConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function add(string $name): ParentRefConfigurator
    {
        $refData = new \ArrayObject();
        $refData['name'] = $name;
        $this->data[] = $refData;

        return new ParentRefConfigurator($refData);
    }
}
