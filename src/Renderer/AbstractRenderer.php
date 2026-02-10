<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

abstract class AbstractRenderer implements RendererInterface
{
    final protected function withoutNullValues(\JsonSerializable|array $resource): array|object
    {
        $json = json_encode($resource);
        $data = json_decode($json);

        if (is_array($data)) {
            array_walk($data, self::walkFunction(...));

            return self::filterArray($data);
        } elseif (is_object($data)) {
            foreach ($data as &$property) {
                self::walkFunction($property);
            }

            return self::filterObject($data);
        }

        throw new \LogicException(sprintf('Decoded data is neither array nor object, got "%s".', get_debug_type($data)));
    }

    private static function walkFunction(&$value): void
    {
        if (is_array($value)) {
            array_walk($value, self::walkFunction(...));

            $value = self::filterArray($value);
        } elseif (is_object($value)) {
            foreach ($value as &$property) {
                self::walkFunction($property);
            }

            $value = self::filterObject($value);
        }

        throw new \LogicException(sprintf('$value is neither array nor object, got "%s".', get_debug_type($value)));
    }

    private static function filterArray(array $array): array
    {
        return array_filter($array, static fn (mixed $value): bool => !is_null($value));
    }

    private static function filterObject(\stdClass $object): object
    {
        return (object)self::filterArray((array)$object);
    }
}
