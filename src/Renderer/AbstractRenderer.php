<?php

declare(strict_types=1);

namespace Klass8s\Renderer;

abstract class AbstractRenderer implements RendererInterface
{
    final protected function withoutNullValues(\JsonSerializable|array $resource): array
    {
        $json = json_encode($resource);
        $data = json_decode($json, true);

        $data = self::filterArray($data);

        array_walk($data, self::walkFunction(...));

        return self::filterArray($data);
    }

    private static function walkFunction(&$value): void
    {
        if (is_array($value)) {
            array_walk($value, self::walkFunction(...));

            $value = self::filterArray($value);
        }
    }

    private static function filterArray(array $array): array
    {
        $parent = $array; // copy argument to a new array
        return array_filter($array, static function (mixed $value, int|string $key) use ($parent): bool {
            if ('emptyDir' === $key && [] === $value) {
                $parentCopy = $parent;
                unset($parentCopy['name']);
                $parentCopy = array_filter(
                    $parentCopy,
                    static fn (mixed $value, int|string $key) => !in_array($value, [null, []], true),
                    ARRAY_FILTER_USE_BOTH
                );

                return empty($parentCopy);
            }

            return !in_array($value, [null, []], true);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
