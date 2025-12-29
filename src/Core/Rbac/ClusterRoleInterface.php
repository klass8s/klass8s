<?php

declare(strict_types=1);

namespace Klass8s\Core\Rbac;

use K8Schema\Api\Rbac\V1\ClusterRole;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Rbac\Configurator\AggregationRuleConfigurator;
use Klass8s\Core\Rbac\Configurator\PolicyRulesConfigurator;

interface ClusterRoleInterface extends ManifestInterface
{
    public function aggregationRule(AggregationRuleConfigurator $rule): void;
    public function rules(PolicyRulesConfigurator $rules): void;
    public function configureClusterRole(ClusterRole $clusterRole): void;
}
