<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\Role;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;
use Klass8s\Core\Rbac\RoleInterface;
use Klass8s\Manifest\ManifestInterface;

final class RoleMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return RoleInterface::class;
    }

    protected function makeResource(ManifestInterface|RoleInterface $manifest): Role
    {
        $role = new Role();
        $app = $manifest->app;
        $app::configureMeta($manifest, $role);
        $rulesConfigurator = new PolicyRulesConfigurator($role->rules());
        $manifest->rules($rulesConfigurator);
        $manifest->configureRole($role);

        return $role;
    }
}
