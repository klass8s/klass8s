<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Affinity;

use K8Schema\Api\Core\V1\Affinity;
use Klass8s\Core\Pod\Affinity\Node\NodeAffinityBuilder;
use Klass8s\Core\Pod\Affinity\Pod\PodAffinityBuilder;

class AffinityConfigurator
{
    private Affinity $affinity;

    public function __construct(Affinity $affinity)
    {
        $this->affinity = $affinity;
    }

    public function nodeAffinity(): NodeAffinityBuilder
    {
        return new NodeAffinityBuilder($this->affinity->nodeAffinity());
    }

    public function podAffinity(): PodAffinityBuilder
    {
        $podAffinity = $this->affinity->podAffinity();
        $preferences = $podAffinity->preferredDuringSchedulingIgnoredDuringExecution();
        $requirements = $podAffinity->requiredDuringSchedulingIgnoredDuringExecution();

        return new PodAffinityBuilder($preferences, $requirements);
    }

    public function podAntiAffinity(): PodAffinityBuilder
    {
        $podAffinity = $this->affinity->podAntiAffinity();
        $preferences = $podAffinity->preferredDuringSchedulingIgnoredDuringExecution();
        $requirements = $podAffinity->requiredDuringSchedulingIgnoredDuringExecution();

        return new PodAffinityBuilder($preferences, $requirements);
    }
}
