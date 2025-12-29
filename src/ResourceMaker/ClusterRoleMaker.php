<?php

declare(strict_types=1);

namespace Klass8s\ResourceMaker;

use K8Schema\Api\Rbac\V1\ClusterRole;
use Klass8s\App\AppInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Rbac\ClusterRoleInterface;
use Klass8s\Core\Rbac\Configurator\AggregationRuleConfigurator;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;

class ClusterRoleMaker extends AbstractResourceMaker
{
    protected function supportsClass(): string
    {
        return ClusterRoleInterface::class;
    }

    protected function makeResource(ManifestInterface|ClusterRoleInterface $manifest, AppInterface $app): ClusterRole
    {
        $role = new ClusterRole();
        $app->metadataHelper()->configureMeta($manifest, $role);
        $rulesConfigurator = new PolicyRulesConfigurator($role->rules());
        $manifest->rules($rulesConfigurator);
        $aggregationRuleConfigurator = new AggregationRuleConfigurator($role->aggregationRule());
        $manifest->aggregationRule($aggregationRuleConfigurator);
        $manifest->configureClusterRole($role);

        return $role;
    }
}
