<?php

declare(strict_types=1);

namespace Klass8s\Value;

use Symfony\Component\Yaml\Yaml;

final readonly class YamlString
{
    private const int INLINE_LEVEL = 5;
    private const int INDENTATION_SPACES = 4;

    /**
     * @var string The YAML formatted string
     */
    public private(set) string $value;

    public function __construct(string $value)
    {
        $this->value = Yaml::dump(Yaml::parse($value), self::INLINE_LEVEL, self::INDENTATION_SPACES);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
