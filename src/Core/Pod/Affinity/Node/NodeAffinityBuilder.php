<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod\Affinity\Node;

use K8Schema\Api\Core\V1\NodeAffinity;
use K8Schema\Api\Core\V1\NodeSelectorTerm;
use K8Schema\Api\Core\V1\PreferredSchedulingTerm;
use Klass8s\Core\Pod\Affinity\NodeAffinityExpression;

class NodeAffinityBuilder
{
    public function __construct(private NodeAffinity $affinity)
    {
    }

    public function addPreference(NodeAffinityExpression $rule, int $preferenceWeight): self
    {
        $preferences = $this->affinity->preferredDuringSchedulingIgnoredDuringExecution();
        $term = new PreferredSchedulingTerm($preferenceWeight);
        $preferences->add($term);
        $selector = $term->preference();

        $list = NodeAffinityExpression::TARGET_FIELD === $rule->target()
            ? $selector->matchFields()
            : $selector->matchExpressions();
        $requirement = $rule->toNodeSelectorRequirement();
        $list->add($requirement);

        return $this;
    }

    public function addRequirement(NodeAffinityExpression $rule): self
    {
        $requirements = $this->affinity->requiredDuringSchedulingIgnoredDuringExecution();
        $selector = new NodeSelectorTerm();
        $requirements->nodeSelectorTerms()->add($selector);

        $list = NodeAffinityExpression::TARGET_FIELD === $rule->target()
            ? $selector->matchFields()
            : $selector->matchExpressions();
        $requirement = $rule->toNodeSelectorRequirement();
        $list->add($requirement);

        return $this;
    }
}
