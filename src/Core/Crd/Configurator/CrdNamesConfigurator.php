<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd\Configurator;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionNames;

class CrdNamesConfigurator
{
    public function __construct(
        private readonly CustomResourceDefinitionNames $names,
    ) {
    }

    public function singular(string $singular): static
    {
        $this->names->setSingular($singular);

        return $this;
    }

    public function listKind(string $listKind): static
    {
        $this->names->setListKind($listKind);

        return $this;
    }

    public function shortNames(string ...$shortNames): static
    {
        foreach ($shortNames as $shortName) {
            $this->names->shortNames()->add($shortName);
        }

        return $this;
    }

    public function categories(string ...$categories): static
    {
        foreach ($categories as $category) {
            $this->names->categories()->add($category);
        }

        return $this;
    }
}
