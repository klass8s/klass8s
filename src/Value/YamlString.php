<?php

declare(strict_types=1);

namespace Klass8s\Value;

use Symfony\Component\Yaml\Yaml;

final readonly class YamlString
{
    private const string DOCUMENT_SEPARATOR = "\n---\n";
    private const int INLINE_LEVEL = 5;
    private const int INDENTATION_SPACES = 4;

    /**
     * @var string The YAML formatted string
     */
    private(set) string $value;

    public function __construct(string $value)
    {
        if (str_contains($value, self::DOCUMENT_SEPARATOR)) {
            $documents = explode(self::DOCUMENT_SEPARATOR, $value) |> array_filter(...);
            array_map(fn (string $document) => Yaml::parse($document), $documents); // validate documents
            $this->value = $value;

            return;
        }

        $this->value = Yaml::dump(Yaml::parse($value), self::INLINE_LEVEL, self::INDENTATION_SPACES);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
