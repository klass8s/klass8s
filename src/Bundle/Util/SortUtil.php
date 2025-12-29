<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Util;

class SortUtil
{
    public static function ksortRecursive(array &$array): void
    {
        ksort($array);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::ksortRecursive($array[$key]);
            }
        }
    }
}
