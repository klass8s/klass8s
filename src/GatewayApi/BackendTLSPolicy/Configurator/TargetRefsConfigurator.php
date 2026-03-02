<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Collection\GenericList;
use Klass8s\Core\Service\ServiceInterface;

final readonly class TargetRefsConfigurator
{
    public function __construct(
        private GenericList $data,
        private AppInterface $app,
    ) {
    }

    public function addService(string $name, ?string $sectionName = null): self
    {
        return $this->add('', 'Service', $name, $sectionName);
    }

    /**
     * @param class-string<ServiceInterface> $serviceClass
     */
    public function addServiceClass(string $serviceClass, ?string $sectionName = null): self
    {
        $name = $this->app->resourceNameFromManifestClass($serviceClass, ServiceInterface::class);

        return $this->addService($name, $sectionName);
    }

    public function add(string $group, string $kind, string $name, ?string $sectionName = null): self
    {
        $ref = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        if (null !== $sectionName) {
            $ref['sectionName'] = $sectionName;
        }

        $this->data->add($ref);

        return $this;
    }
}
