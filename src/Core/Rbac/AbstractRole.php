<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\Role;
use Klass8s\Core\AbstractManifest;

abstract class AbstractRole extends AbstractManifest implements RoleInterface
{
    public function configureRole(Role $role): void
    {
    }

    public static function apiVersion(): string
    {
        return Role::API_VERSION;
    }

    public static function kind(): string
    {
        return Role::KIND;
    }
}
