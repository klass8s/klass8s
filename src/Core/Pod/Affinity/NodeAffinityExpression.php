<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Affinity;

use K8Schema\Api\Core\V1\NodeSelectorRequirement;
use Klass8s\Core\LabelSelector\Operator;

class NodeAffinityExpression extends PodAffinityExpression
{
    public function toNodeSelectorRequirement(): NodeSelectorRequirement
    {
        $requirement = new NodeSelectorRequirement($this->key, $this->operator());
        if (null !== $this->values) {
            $requirement->values()->addAll($this->values);
        }

        return $requirement;
    }

    public function greaterThan(int $value): self
    {
        $this->ensureImmutableOperator();
        $this->operator = Operator::GREATER_THAN;
        $this->values = [$value];

        return $this;
    }

    public function lowerThan(int $value): self
    {
        $this->ensureImmutableOperator();
        $this->operator = Operator::LOWER_THAN;
        $this->values = [$value];

        return $this;
    }
}
