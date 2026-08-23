<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling;

use Klass8s\App\AppManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class AutoscaleRenderTest extends KernelTestCase
{
    /**
     * @var list<array<string, mixed>>
     */
    private array $documents;

    protected static function getKernelClass(): string
    {
        return AutoscaleTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        /** @var AppManager $appManager */
        $appManager = self::getContainer()->get(AppManager::class);
        $yaml = $appManager->generateManifests('autoscale')->render()->toString();
        $this->documents = $this->parseYamlDocuments($yaml);
    }

    public function testScaledObjectIsSpawnedForAttributedWorkload(): void
    {
        $scaler = $this->findDocument('ScaledObject', 'autoscale-worker-scaler');
        self::assertNotNull($scaler, 'ScaledObject for the attributed workload not found in rendered output');

        self::assertSame('keda.sh/v1alpha1', $scaler['apiVersion']);

        $spec = $scaler['spec'];
        self::assertSame('autoscale-worker', $spec['scaleTargetRef']['name']);
        self::assertSame('apps/v1', $spec['scaleTargetRef']['apiVersion']);
        self::assertSame('Deployment', $spec['scaleTargetRef']['kind']);
        self::assertSame(2, $spec['minReplicaCount']);
        self::assertSame(8, $spec['maxReplicaCount']);
        self::assertSame(30, $spec['pollingInterval']);
        self::assertSame(300, $spec['cooldownPeriod']);

        self::assertCount(1, $spec['triggers']);
        $trigger = $spec['triggers'][0];
        self::assertSame('test-lag', $trigger['type']);
        self::assertSame('worker-queue', $trigger['metadata']['queue']);
        // Trigger metadata is a KEDA StringMap — every value is stringified.
        self::assertSame('16', $trigger['metadata']['lagThreshold']);
        self::assertSame('test', $trigger['metadata']['env']);
    }

    public function testSpawnedScaledObjectWentThroughTheRegularPipeline(): void
    {
        $scaler = $this->findDocument('ScaledObject', 'autoscale-worker-scaler');
        self::assertNotNull($scaler);

        // Default metadata labels prove the spawned template was proxied; the klass8s.dev/app
        // annotation proves the generic ManifestGeneratedEvent was dispatched for it.
        self::assertSame('autoscale', $scaler['metadata']['annotations']['klass8s.dev/app'] ?? null);
        self::assertNotEmpty($scaler['metadata']['labels'] ?? []);
    }

    public function testAutoscaledDeploymentHasNoReplicasAndIsAnnotated(): void
    {
        $deployment = $this->findDocument('Deployment', 'autoscale-worker');
        self::assertNotNull($deployment);

        self::assertArrayNotHasKey('replicas', $deployment['spec'], 'spec.replicas must be stripped from an autoscaled Deployment');
        self::assertSame('2-8', $deployment['metadata']['annotations']['klass8s.dev/autoscale'] ?? null);
    }

    public function testAttributeInheritanceIsNearestClassWins(): void
    {
        $scaler = $this->findDocument('ScaledObject', 'autoscale-worker-two-scaler');
        self::assertNotNull($scaler, 'ScaledObject for the inheriting workload not found');

        $spec = $scaler['spec'];
        // Own #[Autoscale] replaces the parent's entirely: max from the child, min falls back
        // to the attribute default (1), not to the parent's 2.
        self::assertSame(1, $spec['minReplicaCount']);
        self::assertSame(3, $spec['maxReplicaCount']);
        self::assertSame('autoscale-worker-two', $spec['scaleTargetRef']['name']);

        // The trigger set is inherited from the parent (the child declares none).
        self::assertCount(1, $spec['triggers']);
        self::assertSame('worker-queue', $spec['triggers'][0]['metadata']['queue']);
    }

    public function testOneAttributeMayFanOutIntoSeveralTriggers(): void
    {
        $scaler = $this->findDocument('ScaledObject', 'autoscale-fanout-worker-scaler');
        self::assertNotNull($scaler, 'ScaledObject for the fan-out workload not found');

        $triggers = $scaler['spec']['triggers'];
        self::assertCount(2, $triggers);
        self::assertSame('queue-one', $triggers[0]['metadata']['queue']);
        self::assertSame('queue-two', $triggers[1]['metadata']['queue']);
    }

    public function testUnattributedDeploymentIsUntouched(): void
    {
        $deployment = $this->findDocument('Deployment', 'autoscale-plain');
        self::assertNotNull($deployment);

        self::assertSame(2, $deployment['spec']['replicas']);
        self::assertArrayNotHasKey('klass8s.dev/autoscale', $deployment['metadata']['annotations'] ?? []);

        foreach ($this->documents as $document) {
            if ('ScaledObject' === $document['kind']) {
                self::assertNotSame('autoscale-plain-scaler', $document['metadata']['name']);
            }
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseYamlDocuments(string $yaml): array
    {
        $documents = [];
        foreach (preg_split('/\n---\n/', $yaml) as $doc) {
            $doc = trim($doc);
            if ('' === $doc) {
                continue;
            }
            $doc = preg_replace('/^#\s*File:.*\n/', '', $doc);
            $parsed = Yaml::parse($doc);
            if (is_array($parsed) && isset($parsed['kind'])) {
                $documents[] = $parsed;
            }
        }

        return $documents;
    }

    /**
     * @param list<array<string, mixed>> $documents
     *
     * @return array<string, mixed>|null
     */
    private function findDocument(string $kind, string $name): ?array
    {
        foreach ($this->documents as $document) {
            if (($document['kind'] ?? null) === $kind && ($document['metadata']['name'] ?? null) === $name) {
                return $document;
            }
        }

        return null;
    }
}
