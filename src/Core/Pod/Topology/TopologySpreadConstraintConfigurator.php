<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Topology;

use K8Schema\Api\Core\V1\TopologySpreadConstraint;
use Klass8s\Core\LabelSelector\SelectorConfigurator;

readonly class TopologySpreadConstraintConfigurator
{
    public function __construct(private TopologySpreadConstraint $constraint)
    {
    }

    public function labelSelector(): SelectorConfigurator
    {
        return new SelectorConfigurator($this->constraint->labelSelector());
    }

    public function matchLabelKeys(string ...$keys): static
    {
        if (0 === count($keys)) {
            throw new \InvalidArgumentException('You must provide at least one label key');
        }

        $this->constraint->matchLabelKeys()->addAll($keys);

        return $this;
    }

    public function setMinDomains(int $minDomains): static
    {
        if ($minDomains <= 0) {
            throw new \InvalidArgumentException('$minDomains must be greater than 0.');
        }

        if ($this->constraint->getWhenUnsatisfiable() !== WhenUnsatisfiable::DoNotSchedule->value) {
            throw new \LogicException('When $minDomains value is set, WhenUnsatisfiable must be "DoNotSchedule".');
        }

        $this->constraint->setMinDomains($minDomains);

        return $this;
    }

    public function nodeAffinityPolicy(NodePolicy $policy): static
    {
        $this->constraint->setNodeAffinityPolicy($policy->value);

        return $this;
    }

    public function nodeTaintsPolicy(NodePolicy $policy): static
    {
        $this->constraint->setNodeTaintsPolicy($policy->value);

        return $this;
    }
}
