<?php

declare(strict_types=1);

namespace Klass8s\Config;

trait ConfigAwareTrait
{
    protected array $config = [];

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
