<?php

declare(strict_types=1);

namespace Klass8s\Value;

use K8Schema\Collection\StringMap;
use Klass8s\Util\StringUtil;

final class StringMapProxy extends StringMap
{
    public function __construct(private readonly StringMap $map)
    {
        parent::__construct();
    }

    public function add(string $name, mixed $value): StringMap
    {
        $value = StringUtil::stringify($value);
        $this->map->add($name, $value);

        return $this;
    }

    public function addAll(array $items): StringMap
    {
        foreach ($items as $key => $value) {
            $this->add($key, $value);
        }

        return $this;
    }

    public function all(): \ArrayObject
    {
        return $this->map->all();
    }

    public function clear(): self
    {
        $this->map->clear();

        return $this;
    }

    public function count(): int
    {
        return $this->map->count();
    }

    public function get(string $name): string
    {
        return $this->map->get($name);
    }

    public function has(string $name): bool
    {
        return $this->map->has($name);
    }

    public function remove(string $name): self
    {
        $this->map->remove($name);

        return $this;
    }

    public function jsonSerialize(): \ArrayObject
    {
        return $this->map->jsonSerialize();
    }

    public static function make(StringMap $map): self
    {
        return new self($map);
    }
}
