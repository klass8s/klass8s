<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Traits;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\LabelSelector;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Proxy\ProxyManager;

trait ConfigureSelectorTrait
{
    private function configureSelector(ManifestInterface $manifest, LabelSelector $specSelector): void
    {
        $selector = new SelectorConfigurator($specSelector);
        $manifest->selector($selector);

        if (0 === $specSelector->matchLabels()->count() && 0 === $specSelector->matchExpressions()->count()) {
            throw new \LogicException(
                sprintf(
                    'Manifest class "%s" does not provide selector labels or expressions. Please implement method "%s"::selector()',
                    ProxyManager::unwrapClassName($manifest),
                    ProxyManager::unwrapClassName($manifest),
                )
            );
        }
    }
}
