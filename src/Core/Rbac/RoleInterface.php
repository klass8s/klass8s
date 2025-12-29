<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\Role;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;

interface RoleInterface extends ManifestInterface
{
    public function rules(PolicyRulesConfigurator $rules): void;
    public function configureRole(Role $role): void;
}
