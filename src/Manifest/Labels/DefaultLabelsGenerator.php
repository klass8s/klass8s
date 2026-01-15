<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Labels;

use Klass8s\Manifest\ManifestInterface;

readonly class DefaultLabelsGenerator implements LabelsGeneratorInterface
{
    public function forManifest(ManifestInterface $manifest): array
    {
        return [
            K8sLabel::Name->value => $manifest->app->alias(),
            K8sLabel::Component->value => $manifest->manifestInstanceName,
        ];
    }
}
