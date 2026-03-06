<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd;

use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinition;
use Klass8s\Core\Crd\Configurator\CrdNamesConfigurator;
use Klass8s\Core\Crd\Configurator\CrdVersionsConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface CustomResourceDefinitionInterface extends ManifestInterface
{
    public function group(): string;

    public function resourceKind(): string;

    public function plural(): string;

    public function scope(): CrdScope;

    public function names(CrdNamesConfigurator $names): void;

    public function versions(CrdVersionsConfigurator $versions): void;

    public function preserveUnknownFields(): ?bool;

    public function configureCrd(CustomResourceDefinition $crd): void;
}
