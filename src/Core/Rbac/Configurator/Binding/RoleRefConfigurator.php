<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac\Configurator\Binding;

use K8Schema\Api\Rbac\V1\ClusterRole;
use K8Schema\Api\Rbac\V1\Role;
use K8Schema\Api\Rbac\V1\RoleRef;
use Klass8s\App\AppInterface;
use Klass8s\Core\Rbac\ClusterRoleInterface;
use Klass8s\Core\Rbac\RoleInterface;
use Klass8s\Registry\AppRegistry;

readonly class RoleRefConfigurator
{
    public function __construct(
        private RoleRef $ref,
        private AppInterface $app,
        private AppRegistry $appRegistry,
    ) {
        $apiGroup = substr(Role::API_VERSION, 0, strpos(Role::API_VERSION, '/'));
        $ref->setApiGroup($apiGroup);
    }

    public function fromRoleName(string $roleName): void
    {
        $this->ref->setKind(Role::KIND)->setName($roleName);
    }

    public function fromClusterRoleName(string $clusterRoleName): void
    {
        $this->ref->setKind(ClusterRole::KIND)->setName($clusterRoleName);
    }

    /**
     * @param class-string<RoleInterface> $roleClass
     */
    public function fromRoleClass(string $roleClass): void
    {
        $this->ensureAppOwnsManifestClass($roleClass);
        $name = $this->app->resourceNameByManifestClass($roleClass, RoleInterface::class);

        $this->fromRoleName($name);
    }

    /**
     * @param class-string<ClusterRoleInterface> $clusterRoleClass
     */
    public function fromClusterRoleClass(string $clusterRoleClass): void
    {
        $this->ensureAppOwnsManifestClass($clusterRoleClass);
        $name = $this->app->resourceNameByManifestClass($clusterRoleClass, ClusterRoleInterface::class);

        $this->fromClusterRoleName($name);
    }

    public function withExternalApp(string $appAlias): RoleRefConfigurator
    {
        return new RoleRefConfigurator($this->ref, $this->appRegistry->get($appAlias), $this->appRegistry);
    }

    private function ensureAppOwnsManifestClass(string $className): void
    {
        if ($this->app->ownsManifestClass($className)) {
            return;
        }

        $msg = <<<'ERR'
            App "%s" does not have manifests with class "%s". Please use method "%s::withExternalApp()"
            for referencing manifests from other apps.

            $ref->withExternalApp('externalAppAlias')->fromRoleClass(MyRole::class)
            ERR;

        throw new \InvalidArgumentException(
            sprintf($msg, $this->app->alias(), $className, self::class)
        );
    }
}
