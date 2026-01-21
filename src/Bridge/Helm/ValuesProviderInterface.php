<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm;

interface ValuesProviderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function values(): array;
}
