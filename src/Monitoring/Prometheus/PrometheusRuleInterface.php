<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus;

use Klass8s\Core\ManifestInterface;
use Klass8s\Monitoring\Prometheus\Configurator\RuleGroupsConfigurator;

interface PrometheusRuleInterface extends ManifestInterface
{
    public function groups(RuleGroupsConfigurator $groups): void;
}
