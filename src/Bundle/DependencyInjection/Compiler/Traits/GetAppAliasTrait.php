<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler\Traits;

use Klass8s\Bundle\Klass8sBundle;

trait GetAppAliasTrait
{
    private function getAppAlias(string $serviceId, array $tags): string
    {
        $alias = null;
        foreach ($tags as $tag) {
            if (array_key_exists('alias', $tag)) {
                $alias = $tag['alias'];
                break;
            }
        }
        if (null === $alias) {
            throw new \LogicException(sprintf('"%s" tag on app service "%s" does not have "alias" attribute.', Klass8sBundle::TAG_APP, $serviceId));
        }

        return $alias;
    }
}
