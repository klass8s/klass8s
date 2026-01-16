<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\ClusterRoleBinding;
use Klass8s\Manifest\AbstractManifest;

abstract class AbstractClusterRoleBinding extends AbstractManifest implements ClusterRoleBindingInterface
{
    public static function apiVersion(): string
    {
        return ClusterRoleBinding::API_VERSION;
    }

    public static function kind(): string
    {
        return ClusterRoleBinding::KIND;
    }
}
