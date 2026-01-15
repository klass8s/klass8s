<?php

declare(strict_types=1);

namespace Klass8s\Core\Deployment;

use K8Schema\Api\Apps\V1\Deployment;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Pod\PodTemplateSpecInterface;
use Klass8s\Manifest\ManifestInterface;

interface DeploymentInterface extends PodTemplateSpecInterface, ManifestInterface
{
    public function selector(SelectorConfigurator $selector): void;
    public function replicas(): int;
    public function revisionHistoryLimit(): int;
    public function minReadySeconds(): ?int;
    public function progressDeadlineSeconds(): ?int;
    public function configureDeployment(Deployment $deployment): void;
    public function strategy(StrategyConfigurator $strategy): void;
}
