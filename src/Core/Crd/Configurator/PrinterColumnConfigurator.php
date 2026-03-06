<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd\Configurator;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceColumnDefinition;

class PrinterColumnConfigurator
{
    public function __construct(
        private readonly CustomResourceColumnDefinition $column,
    ) {
    }

    public function description(string $description): static
    {
        $this->column->setDescription($description);

        return $this;
    }

    public function format(string $format): static
    {
        $this->column->setFormat($format);

        return $this;
    }

    public function priority(int $priority): static
    {
        $this->column->setPriority($priority);

        return $this;
    }
}
