<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\Role;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;
use Klass8s\Core\Rbac\RoleInterface;

class RoleMaker extends AbstractResourceMaker
{
    protected function supportsClass(): string
    {
        return RoleInterface::class;
    }

    protected function makeResource(ManifestInterface|RoleInterface $manifest, AppInterface $app): Role
    {
        $role = new Role();
        $app->metadataHelper()->configureMeta($manifest, $role);
        $rulesConfigurator = new PolicyRulesConfigurator($role->rules());
        $manifest->rules($rulesConfigurator);
        $manifest->configureRole($role);

        return $role;
    }
}
