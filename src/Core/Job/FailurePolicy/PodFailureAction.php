<?php

declare(strict_types=1);

namespace Klass8s\Core\Job\FailurePolicy;

/**
 * What a `podFailurePolicy` rule does with a failed pod that matches it — `batch/v1`
 * `PodFailurePolicyRule.action`.
 */
enum PodFailureAction: string
{
    /** The Job fails at once, retries left or not. */
    case FailJob = 'FailJob';
    /** The pod's index fails (indexed Jobs with `backoffLimitPerIndex`). */
    case FailIndex = 'FailIndex';
    /** The failure does not count towards `backoffLimit`: the pod is retried. */
    case Ignore = 'Ignore';
    /** The failure counts, like a pod no rule matches. */
    case Count = 'Count';
}
