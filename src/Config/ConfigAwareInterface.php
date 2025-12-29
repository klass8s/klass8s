<?php

declare(strict_types=1);

namespace Klass8s\Config;

interface ConfigAwareInterface
{
    public function setConfig(array $config): void;
}
