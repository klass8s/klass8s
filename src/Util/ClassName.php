<?php

declare(strict_types=1);

namespace Klass8s\Util;

use Proximity\ProxyInterface;

class ClassName
{
    public static function real(object $object): string
    {
        $class = new \ReflectionObject($object);
        if ($class->implementsInterface(ProxyInterface::class)) {
            $class = $class->getParentClass();
        }

        return $class->getName();
    }
}
