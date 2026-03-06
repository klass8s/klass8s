<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd\Configurator;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceColumnDefinition;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionVersion;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceValidation;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\SelectableField;
use Klass8s\Core\Crd\JsonSchemaArray;

class CrdVersionConfigurator
{
    public function __construct(
        private readonly CustomResourceDefinitionVersion $version,
    ) {
    }

    /**
     * @param array<string, mixed> $schema Raw OpenAPI v3 schema array
     */
    public function openApiV3Schema(array $schema): static
    {
        $validation = new CustomResourceValidation();
        $validation->setOpenAPIV3Schema(new JsonSchemaArray($schema));
        $this->version->setSchema($validation);

        return $this;
    }

    public function deprecated(bool $deprecated = true, ?string $warning = null): static
    {
        $this->version->setDeprecated($deprecated);
        if (null !== $warning) {
            $this->version->setDeprecationWarning($warning);
        }

        return $this;
    }

    public function addPrinterColumn(string $name, string $type, string $jsonPath): PrinterColumnConfigurator
    {
        $column = new CustomResourceColumnDefinition($jsonPath, $name, $type);
        $this->version->additionalPrinterColumns()->add($column);

        return new PrinterColumnConfigurator($column);
    }

    public function subresources(): SubresourcesConfigurator
    {
        return new SubresourcesConfigurator($this->version);
    }

    public function addSelectableField(string $jsonPath): static
    {
        $field = new SelectableField($jsonPath);
        $this->version->selectableFields()->add($field);

        return $this;
    }
}
