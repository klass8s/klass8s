<?php

declare(strict_types=1);

namespace Klass8s\Collection;

/**
 * @template T
 */
final class GenericList implements \JsonSerializable, \Countable
{
    /** @var list<T> */
    private array $items = [];

    /** @param T $item */
    public function add(mixed $item): void
    {
        $this->items[] = $item;
    }

    public function count(): int
    {
        return count($this->items);
    }

    /** @return list<T> */
    public function jsonSerialize(): array
    {
        return $this->items;
    }
}
