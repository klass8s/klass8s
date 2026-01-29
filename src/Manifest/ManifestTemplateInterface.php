<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

/**
 * Manifests classes, which implement this interface, are used to create many different instances of manifests.
 * Therefore, their identity cannot be determined by class name alone.
 * They cannot be linked by class name, and their method `shortName()` must throw an exception, if called.
 */
interface ManifestTemplateInterface
{
    protected(set) string $dynamicShortName { get; set; }
}
