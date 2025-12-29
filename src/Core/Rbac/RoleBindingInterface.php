<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use Klass8s\Core\Rbac\Configurator\Binding\SubjectsConfigurator;
use Klass8s\Core\Rbac\Configurator\Binding\RoleRefConfigurator;

interface RoleBindingInterface
{
    public function roleRef(RoleRefConfigurator $ref): void;
    public function subjects(SubjectsConfigurator $subjects): void;
}
