<?php

declare(strict_types=1);

namespace Klass8s\Tests\Core\Job;

use K8Schema\Api\Batch\V1\JobSpec;
use K8Schema\Api\Batch\V1\PodFailurePolicy;
use Klass8s\Core\Job\FailurePolicy\PodFailureAction;
use Klass8s\Core\Job\FailurePolicy\PodFailurePolicyConfigurator;
use PHPUnit\Framework\TestCase;

final class PodFailurePolicyConfiguratorTest extends TestCase
{
    public function testIgnoreDisruptionsIsOneRuleOnTheDisruptionTargetCondition(): void
    {
        $policy = new PodFailurePolicy();
        $configurator = new PodFailurePolicyConfigurator($policy);
        self::assertTrue($configurator->isEmpty());

        $configurator->ignoreDisruptions();

        self::assertFalse($configurator->isEmpty());
        self::assertSame(
            ['rules' => [['action' => 'Ignore', 'onPodConditions' => [['status' => 'True', 'type' => 'DisruptionTarget']]]]],
            self::withoutNulls(json_decode((string) json_encode($policy), true)),
            'status is written on every schema: 1.34 requires it, 1.35 defaults it',
        );
    }

    public function testRulesKeepTheOrderTheyWereAddedIn(): void
    {
        $policy = new PodFailurePolicy();
        new PodFailurePolicyConfigurator($policy)
            ->onPodCondition(PodFailureAction::FailJob, 'ConfigIssue', 'True')
            ->ignoreDisruptions();

        self::assertSame(['FailJob', 'Ignore'], array_map(static fn ($r) => $r->getAction(), $policy->rules()->all()));
    }

    public function testOfASpecReusesItsPolicyOrSetsAFreshOne(): void
    {
        $spec = new JobSpec();
        self::assertNull($spec->getPodFailurePolicy());

        PodFailurePolicyConfigurator::of($spec)->ignoreDisruptions();
        $policy = $spec->getPodFailurePolicy();
        self::assertNotNull($policy);
        self::assertSame(1, $policy->rules()->count());

        PodFailurePolicyConfigurator::of($spec)->onPodCondition(PodFailureAction::Count, 'Other');
        self::assertSame($policy, $spec->getPodFailurePolicy(), 'the existing policy is extended, never replaced');
        self::assertSame(2, $policy->rules()->count());
    }

    /**
     * @param array<mixed> $value
     *
     * @return array<mixed>
     */
    private static function withoutNulls(array $value): array
    {
        foreach ($value as $k => $v) {
            if (null === $v) {
                unset($value[$k]);
            } elseif (\is_array($v)) {
                $value[$k] = self::withoutNulls($v);
            }
        }

        return $value;
    }
}
