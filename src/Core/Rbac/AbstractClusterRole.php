<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\ClusterRole;
use Klass8s\Core\AbstractManifest;
use Klass8s\Core\Rbac\Configurator\AggregationRuleConfigurator;

abstract class AbstractClusterRole extends AbstractManifest implements ClusterRoleInterface
{
    public function aggregationRule(AggregationRuleConfigurator $rule): void
    {
    }

    public function configureClusterRole(ClusterRole $clusterRole): void
    {
    }

    public static function apiVersion(): string
    {
        return ClusterRole::API_VERSION;
    }

    public static function kind(): string
    {
        return ClusterRole::KIND;
    }
}
