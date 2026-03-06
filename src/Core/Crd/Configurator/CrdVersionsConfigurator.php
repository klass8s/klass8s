<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd\Configurator;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionVersion;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionVersionList;

class CrdVersionsConfigurator
{
    public function __construct(
        private readonly CustomResourceDefinitionVersionList $versions,
    ) {
    }

    public function addVersion(string $name, bool $served = true, bool $storage = true): CrdVersionConfigurator
    {
        $version = new CustomResourceDefinitionVersion($name, $served, $storage);
        $this->versions->add($version);

        return new CrdVersionConfigurator($version);
    }
}
