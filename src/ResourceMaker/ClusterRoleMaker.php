<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\ClusterRole;
use Klass8s\Core\Rbac\ClusterRoleInterface;
use Klass8s\Core\Rbac\Configurator\AggregationRuleConfigurator;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;
use Klass8s\Manifest\ManifestInterface;

final class ClusterRoleMaker extends AbstractResourceMaker
{
    protected function supportedClass(): string
    {
        return ClusterRoleInterface::class;
    }

    protected function makeResource(ManifestInterface|ClusterRoleInterface $manifest): ClusterRole
    {
        $role = new ClusterRole();
        $app = $manifest->app;
        $app::configureMeta($manifest, $role);
        $rulesConfigurator = new PolicyRulesConfigurator($role->rules());
        $manifest->rules($rulesConfigurator);
        $aggregationRuleConfigurator = new AggregationRuleConfigurator($role->aggregationRule());
        $manifest->aggregationRule($aggregationRuleConfigurator);
        $manifest->configureClusterRole($role);

        return $role;
    }
}
