<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\GatewayApi\Gateway\GatewayInterface;

final class ParentRefsConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function add(string $name): ParentRefConfigurator
    {
        $refData = new \ArrayObject();
        $refData['name'] = $name;
        $this->data[] = $refData;

        return new ParentRefConfigurator($refData);
    }

    /**
     * @param class-string<GatewayInterface> $gatewayClass
     */
    public function addFromClass(string $gatewayClass): ParentRefConfigurator
    {
        $name = $this->app->resourceNameFromManifestClass($gatewayClass, GatewayInterface::class);

        return $this->add($name);
    }
}
