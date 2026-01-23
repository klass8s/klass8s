<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart\Reference;

use Klass8s\Bridge\Helm\Chart\ChartReferenceType;

final readonly class ChartReference
{
    private const string NAME_PATTERN = '#^[A-Za-z0-9._-]+(?:/[A-Za-z0-9._-]+)*$#';

    private(set) ChartReferenceType $type;

    public function __construct(public string $value, public ?string $version = null)
    {
        $this->type = match (true) {
            str_starts_with($value, '.') => ChartReferenceType::RelativePath,
            str_starts_with($value, '/') => ChartReferenceType::AbsolutePath,
            str_starts_with($this->value, 'https://') => ChartReferenceType::Url,
            str_starts_with($this->value, 'oci://') => ChartReferenceType::OciRegistry,
            (bool)preg_match(self::NAME_PATTERN, $value) => ChartReferenceType::ChartName,
            default => throw new \InvalidArgumentException(sprintf('Invalid chart reference "%s". Expected one of: "repository/chart", "https://...", "oci://...", or "./local/path".', $value)),
        };
    }

    public function isRelativePath(): bool
    {
        return ChartReferenceType::RelativePath === $this->type;
    }

    public function isAbsolutePath(): bool
    {
        return ChartReferenceType::AbsolutePath === $this->type;
    }

    public function isUrl(): bool
    {
        return ChartReferenceType::Url === $this->type;
    }

    public function isOciRegistry(): bool
    {
        return ChartReferenceType::OciRegistry === $this->type;
    }

    public function isChartName(): bool
    {
        return ChartReferenceType::ChartName === $this->type;
    }
}
