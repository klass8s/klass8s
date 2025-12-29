<?php

declare(strict_types=1);

namespace Klass8s\App;

interface TenantAwareAppInterface
{
    public function tenant(): AppTenantInterface;

    public function tenantName(): string;
}
