<?php

declare(strict_types=1);

namespace Klass8s\Core\Deployment;

use K8Schema\Api\Apps\V1\Deployment;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\Pod\PodTemplateSpecInterface;

interface DeploymentInterface extends PodTemplateSpecInterface, ManifestInterface
{
    public function selector(SelectorConfigurator $selector): void;
    public function replicas(): int;
    public function revisionHistoryLimit(): int;
    public function minReadySeconds(): int|null;
    public function progressDeadlineSeconds(): int|null;
    public function configureDeployment(Deployment $deployment): void;
    public function strategy(StrategyConfigurator $strategy): void;
}
