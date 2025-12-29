<?php

declare(strict_types=1);

namespace Klass8s\App;

interface AppTenantInterface
{
    public function name(): string;

    public function appAlias(): string;
}
