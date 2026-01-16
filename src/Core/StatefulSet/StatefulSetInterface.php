<?php

declare(strict_types=1);

namespace Klass8s\Core\StatefulSet;

use K8Schema\Api\Apps\V1\StatefulSet;
use Klass8s\Core\LabelSelector\SelectorConfigurator;
use Klass8s\Core\Persistence\PersistentVolumeClaimInterface;
use Klass8s\Core\Pod\PodTemplateSpecInterface;
use Klass8s\Core\StatefulSet\UpdateStrategy\UpdateStrategyConfigurator;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Manifest\Reference\ManifestClassReference;

interface StatefulSetInterface extends PodTemplateSpecInterface, ManifestInterface
{
    public function podManagementPolicy(): ?PodManagementPolicy;
    public function replicas(): int;
    public function revisionHistoryLimit(): int;
    public function selector(SelectorConfigurator $selector): void;
    public function serviceName(): ManifestClassReference;
    public function updateStrategy(UpdateStrategyConfigurator $updateStrategy): void;

    /**
     * @return PersistentVolumeClaimInterface[]
     */
    public function volumeClaimTemplates(): iterable;

    public function configureStatefulSet(StatefulSet $statefulSet): void;
}
