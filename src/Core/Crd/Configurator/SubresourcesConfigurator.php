<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd\Configurator;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionVersion;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceSubresourceScale;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceSubresourceStatus;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceSubresources;

class SubresourcesConfigurator
{
    private readonly CustomResourceSubresources $subresources;

    public function __construct(
        private readonly CustomResourceDefinitionVersion $version,
    ) {
        $this->subresources = $this->version->getSubresources() ?? new CustomResourceSubresources();
        $this->version->setSubresources($this->subresources);
    }

    public function enableStatus(): static
    {
        $this->subresources->setStatus(new CustomResourceSubresourceStatus());

        return $this;
    }

    public function scale(string $specReplicasPath, string $statusReplicasPath, ?string $labelSelectorPath = null): static
    {
        $scale = new CustomResourceSubresourceScale($specReplicasPath, $statusReplicasPath);
        if (null !== $labelSelectorPath) {
            $scale->setLabelSelectorPath($labelSelectorPath);
        }
        $this->subresources->setScale($scale);

        return $this;
    }
}
