<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Traits;

use K8Schema\Collection\StringMap;

trait PrefixMapKeysTrait
{
    private function prefixMapKeys(string $prefix, StringMap $map): void
    {
        foreach ($map->all() as $key => $value) {
            $newKey = $prefix.$key;
            $map->remove($key);
            $map->add($newKey, $value);
        }
    }
}
