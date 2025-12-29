<?php

declare(strict_types=1);

namespace Klass8s\Core\Deployment;

use K8Schema\Api\Apps\V1\DeploymentStrategy;
use Klass8s\Core\Deployment\ValueObject\NumberOrPercents;

readonly class StrategyConfigurator
{
    private const string TYPE_ROLLING_UPDATE = 'RollingUpdate';
    private const string TYPE_RECREATE = 'Recreate';

    public function __construct(private DeploymentStrategy $deploymentStrategy)
    {
    }

    public function rollingUpdate(NumberOrPercents $maxSurge, NumberOrPercents $maxUnavailable): void
    {
        $this->deploymentStrategy
            ->setType(self::TYPE_ROLLING_UPDATE)
            ->rollingUpdate()
                ->setMaxSurge($maxSurge->value)
                ->setMaxUnavailable($maxUnavailable->value);
    }

    public function recreate(): void
    {
        $this->deploymentStrategy->setType(self::TYPE_RECREATE);
    }
}
