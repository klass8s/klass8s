<?php

declare(strict_types=1);

namespace Klass8s\Config;

interface ConfigAwareInterface
{
    /**
     * @var array<string, mixed>
     */
    protected(set) array $config {
        get;
        set;
    }

    public function setConfig(array $config): void;
}
