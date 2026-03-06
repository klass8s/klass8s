<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\APIResourceInterface;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinition;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionNames;
use K8Schema\ApiextensionsApiserver\Pkg\Apis\Apiextensions\V1\CustomResourceDefinitionSpec;
use Klass8s\Core\Crd\Configurator\CrdNamesConfigurator;
use Klass8s\Core\Crd\Configurator\CrdVersionsConfigurator;
use Klass8s\Core\Crd\CustomResourceDefinitionInterface;
use Klass8s\Event\CustomResourceDefinitionGeneratedEvent;
use Klass8s\Manifest\ManifestInterface;

final class CustomResourceDefinitionMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return CustomResourceDefinitionInterface::class;
    }

    protected function makeResource(ManifestInterface|CustomResourceDefinitionInterface $manifest): APIResourceInterface
    {
        $names = new CustomResourceDefinitionNames($manifest->resourceKind(), $manifest->plural());
        $spec = new CustomResourceDefinitionSpec($manifest->group(), $names, $manifest->scope()->value);
        $crd = new CustomResourceDefinition($spec);

        $app = $manifest->app;
        $app::configureMeta($manifest, $crd);

        $namesConfigurator = new CrdNamesConfigurator($names);
        $manifest->names($namesConfigurator);

        $versionsConfigurator = new CrdVersionsConfigurator($spec->versions());
        $manifest->versions($versionsConfigurator);

        $preserveUnknownFields = $manifest->preserveUnknownFields();
        if (null !== $preserveUnknownFields) {
            $spec->setPreserveUnknownFields($preserveUnknownFields);
        }

        $manifest->configureCrd($crd);

        $this->dispatcher->dispatch(
            new CustomResourceDefinitionGeneratedEvent($manifest, $crd, $app),
            CustomResourceDefinitionGeneratedEvent::NAME,
        );

        return $crd;
    }
}
