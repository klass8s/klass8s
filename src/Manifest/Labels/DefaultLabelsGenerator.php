<?php

declare(strict_types=1);

namespace Klass8s\Manifest\Labels;

use Klass8s\Manifest\FullNameAwareInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\ManifestTemplateInterface;

readonly class DefaultLabelsGenerator implements LabelsGeneratorInterface
{
    public function forManifest(ManifestInterface $manifest): array
    {
        // The component label is the manifest's short name: the dynamic one of a template
        // (a template may ALSO be full-name aware — a data-driven set whose instances carry
        // fixed names, e.g. one runner set per org named after the org — and its static
        // shortName() throws by design), the static one of a full-name-aware manifest (its
        // full name is not its role), the computed one otherwise.
        $component = match (true) {
            $manifest instanceof ManifestTemplateInterface => $manifest->dynamicShortName,
            $manifest instanceof FullNameAwareInterface => $manifest::shortName(),
            default => $manifest->computedShortName,
        };

        return [
            K8sLabel::Name->value => $manifest->app->alias(),
            K8sLabel::Component->value => $component,
        ];
    }
}
