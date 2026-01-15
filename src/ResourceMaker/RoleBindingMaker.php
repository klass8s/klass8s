<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\RoleBinding;
use K8Schema\Api\Rbac\V1\RoleRef;
use Klass8s\Core\Rbac\RoleBindingInterface;

class RoleBindingMaker extends AbstractRoleBindingMaker
{
    protected function roleBinding(RoleRef $ref): RoleBinding
    {
        return new RoleBinding($ref);
    }

    protected function supportedClass(): string
    {
        return RoleBindingInterface::class;
    }
}
