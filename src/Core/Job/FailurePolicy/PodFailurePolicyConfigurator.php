<?php

declare(strict_types=1);

namespace Klass8s\Core\Job\FailurePolicy;

use K8Schema\Api\Batch\V1\JobSpec;
use K8Schema\Api\Batch\V1\PodFailurePolicy;
use K8Schema\Api\Batch\V1\PodFailurePolicyOnPodConditionsPattern;
use K8Schema\Api\Batch\V1\PodFailurePolicyRule;
use K8Schema\K8SApi;

/**
 * A Job's `podFailurePolicy`, rule by rule, in the order they are added — the API
 * takes the first rule that matches a failed pod. The API accepts a policy only with
 * `restartPolicy: Never` on the pod template.
 *
 * The one rule nearly every Job wants is {@see ignoreDisruptions()}: a pod the cluster
 * took away (a node drained, a preemption, an eviction) is not the workload's failure
 * and must not eat its `backoffLimit`.
 */
readonly class PodFailurePolicyConfigurator
{
    public function __construct(private PodFailurePolicy $policy)
    {
    }

    /**
     * The configurator over a Job spec that already exists — for the code that shapes
     * a generated Job after the fact (an event subscriber): the spec's policy where it
     * has one, else a fresh one set on it.
     */
    public static function of(JobSpec $spec): static
    {
        $policy = $spec->getPodFailurePolicy();
        if (null === $policy) {
            $policy = new PodFailurePolicy();
            $spec->setPodFailurePolicy($policy);
        }

        return new static($policy);
    }

    /**
     * A pod with the `DisruptionTarget` condition — disrupted by the cluster, not
     * failed by the workload — is retried without counting towards `backoffLimit`.
     */
    public function ignoreDisruptions(): static
    {
        return $this->onPodCondition(PodFailureAction::Ignore, 'DisruptionTarget');
    }

    /**
     * A rule on a pod condition: `$action` for a failed pod whose condition `$type`
     * has `$status` (the API default is `True`; it is always written, so the render
     * is the same on every schema).
     */
    public function onPodCondition(PodFailureAction $action, string $type, string $status = 'True'): static
    {
        $rule = new PodFailurePolicyRule($action->value);
        $rule->onPodConditions()->add(self::pattern($type, $status));
        $this->policy->rules()->add($rule);

        return $this;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->policy->rules()->count();
    }

    /**
     * Up to Kubernetes 1.34 the pattern's constructor takes `(status, type)`; from 1.35
     * `(type)`, `status` optional — the API version decides, as everywhere in klass8s.
     */
    private static function pattern(string $type, string $status): PodFailurePolicyOnPodConditionsPattern
    {
        $pattern = K8SApi::versionIsLowerThan('1.35')
            ? new PodFailurePolicyOnPodConditionsPattern($status, $type)
            : new PodFailurePolicyOnPodConditionsPattern($type);
        $pattern->setStatus($status);

        return $pattern;
    }
}
