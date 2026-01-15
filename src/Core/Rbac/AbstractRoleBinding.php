<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\RoleBinding;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractRoleBinding extends AbstractManifest implements RoleBindingInterface
{
    public static function apiVersion(): string
    {
        return RoleBinding::API_VERSION;
    }

    public static function kind(): string
    {
        return RoleBinding::KIND;
    }
}
