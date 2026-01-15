<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\ClusterRoleBinding;
use K8Schema\Api\Rbac\V1\RoleRef;
use Klass8s\Core\Rbac\ClusterRoleBindingInterface;

class ClusterRoleBindingMaker extends AbstractRoleBindingMaker
{
    protected function roleBinding(RoleRef $ref): ClusterRoleBinding
    {
        return new ClusterRoleBinding($ref);
    }

    protected function supportedClass(): string
    {
        return ClusterRoleBindingInterface::class;
    }
}
