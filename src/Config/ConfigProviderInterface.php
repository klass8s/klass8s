<?php

declare(strict_types=1);

namespace Klass8s\Config;

interface ConfigProviderInterface
{
    public function config(): array;
}
