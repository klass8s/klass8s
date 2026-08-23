<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling;

use K8Schema\Api\Apps\V1\Deployment;
use Klass8s\App\AppInterface;
use Klass8s\Autoscaling\Attribute\Autoscale;
use Klass8s\Autoscaling\AttributeAutoscalePolicy;
use Klass8s\Autoscaling\AutoscaleDecision;
use Klass8s\Autoscaling\AutoscalePolicyInterface;
use Klass8s\Autoscaling\ScalingTriggerResolversRegistry;
use Klass8s\Bundle\EventListener\AutoscaleSubscriber;
use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Event\DeploymentGeneratedEvent;
use Klass8s\Manifest\ManifestProcessor;
use Klass8s\Manifest\ManifestSpawner;
use Klass8s\Proxy\ProxyFactory;
use Klass8s\Tests\Bundle\Autoscaling\Fixture\AutoscaleApp\ScaledWorkerDeployment;
use Klass8s\Tests\Bundle\Autoscaling\Fixture\Unit\NonScalableWorkerDeployment;
use Klass8s\Tests\Bundle\Autoscaling\Fixture\Unit\TriggerlessWorkerDeployment;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AutoscaleSubscriberTest extends TestCase
{
    public function testAutoscaleOnNonScalableWorkloadFailsTheBuild(): void
    {
        $subscriber = $this->subscriber();
        $event = $this->deploymentEvent(new NonScalableWorkerDeployment());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/must not be scaled horizontally/');
        $subscriber->onDeploymentGenerated($event);
    }

    public function testAutoscaleWithoutTriggerAttributesFailsTheBuild(): void
    {
        $subscriber = $this->subscriber();
        $event = $this->deploymentEvent(new TriggerlessWorkerDeployment());

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/declares no scaling trigger attribute|neither it nor any of its parents declares a scaling trigger attribute/');
        $subscriber->onDeploymentGenerated($event);
    }

    public function testDisablingPolicyKeepsReplicasAndAnnotatesOff(): void
    {
        $policy = new class implements AutoscalePolicyInterface {
            public function decide(Autoscale $attribute, DeploymentInterface $manifest): AutoscaleDecision
            {
                return AutoscaleDecision::disabled();
            }
        };
        $subscriber = $this->subscriber($policy);

        $deployment = new Deployment();
        $deployment->spec()->setReplicas(3);
        $event = $this->deploymentEvent(new ScaledWorkerDeployment(), $deployment);

        $subscriber->onDeploymentGenerated($event);

        self::assertSame(3, $deployment->spec()->getReplicas(), 'a disabled decision must leave spec.replicas alone');
        self::assertSame('off', $deployment->metadata()->annotations()->all()['klass8s.dev/autoscale'] ?? null);
    }

    public function testEnabledDecisionStripsReplicasAndAnnotatesBounds(): void
    {
        $subscriber = $this->subscriber();

        $deployment = new Deployment();
        $deployment->spec()->setReplicas(3);
        $event = $this->deploymentEvent(new ScaledWorkerDeployment(), $deployment);

        $subscriber->onDeploymentGenerated($event);

        self::assertNull($deployment->spec()->getReplicas(), 'an enabled decision must strip spec.replicas');
        self::assertSame('2-8', $deployment->metadata()->annotations()->all()['klass8s.dev/autoscale'] ?? null);
    }

    public function testAutoscaleAttributeValidatesItsValues(): void
    {
        $this->expectException(\LogicException::class);
        new Autoscale(maxReplicas: 4, minReplicas: 5);
    }

    public function testAutoscaleDecisionValidatesBounds(): void
    {
        $this->expectException(\LogicException::class);
        AutoscaleDecision::enabled(minReplicas: 2, maxReplicas: 1);
    }

    private function subscriber(?AutoscalePolicyInterface $policy = null): AutoscaleSubscriber
    {
        return new AutoscaleSubscriber(
            policy: $policy ?? new AttributeAutoscalePolicy(),
            triggerResolvers: new ScalingTriggerResolversRegistry([]),
            spawner: new ManifestSpawner(
                $this->createStub(ProxyFactory::class),
                new ManifestProcessor(new EventDispatcher(), []),
            ),
            annotationDomain: 'klass8s.dev',
            currentEnv: 'test',
        );
    }

    private function deploymentEvent(DeploymentInterface $manifest, ?Deployment $deployment = null): DeploymentGeneratedEvent
    {
        return new DeploymentGeneratedEvent(
            $manifest,
            $deployment ?? new Deployment(),
            $this->createStub(AppInterface::class),
        );
    }
}
