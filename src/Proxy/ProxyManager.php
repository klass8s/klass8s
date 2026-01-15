<?php

declare(strict_types=1);

namespace Klass8s\Proxy;

use Proximity\ProxyInterface;

final readonly class ProxyManager
{
    private function __construct()
    {
    }

    /**
     * Gets the object's class, unwrapping it from a proxy if needed.
     *
     * @template T of object
     * @param T $object
     * @return class-string<T>
     */
    public static function unwrapClassName(object $object): string
    {
        if (!$object instanceof ProxyInterface) {
            return $object::class;
        }

        return new \ReflectionClass($object)->getParentClass()->getName();
    }
}
