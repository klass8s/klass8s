<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker\Traits;

use K8Schema\Collection\StringMap;

trait PrefixMapKeysTrait
{
    private function prefixMapKeys(string $prefix, StringMap $map): void
    {
        $keysToRemove = [];
        $dataToAdd = [];
        foreach ($map->all() as $key => $value) {
            $newKey = $prefix.$key;
            $dataToAdd[$newKey] = $value;
            $keysToRemove[] = $key;
        }

        foreach ($keysToRemove as $key) {
            $map->remove($key);
        }

        $map->addAll($dataToAdd);
    }
}
