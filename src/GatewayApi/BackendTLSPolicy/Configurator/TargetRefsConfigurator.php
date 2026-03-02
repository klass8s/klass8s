<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Core\Service\ServiceInterface;

final class TargetRefsConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
    }

    public function addService(string $name, ?string $sectionName = null): static
    {
        return $this->add('', 'Service', $name, $sectionName);
    }

    /**
     * @param class-string<ServiceInterface> $serviceClass
     */
    public function addServiceClass(string $serviceClass, ?string $sectionName = null): static
    {
        $name = $this->app->resourceNameFromManifestClass($serviceClass, ServiceInterface::class);

        return $this->addService($name, $sectionName);
    }

    public function add(string $group, string $kind, string $name, ?string $sectionName = null): static
    {
        $ref = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        if (null !== $sectionName) {
            $ref['sectionName'] = $sectionName;
        }

        $this->data[] = $ref;

        return $this;
    }
}
