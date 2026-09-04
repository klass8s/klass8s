<?php

declare(strict_types=1);

namespace Klass8s\Tests\Storage;

use Klass8s\App\AppProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class StorageClassRenderTest extends KernelTestCase
{
    private AppProcessor $appProcessor;

    protected static function getKernelClass(): string
    {
        return StorageTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->appProcessor = self::getContainer()->get(AppProcessor::class);
    }

    public function testStorageClassRendering(): void
    {
        $storageClass = $this->findStorageClass('storage-fast');

        self::assertSame('storage.k8s.io/v1', $storageClass['apiVersion']);
        self::assertSame('StorageClass', $storageClass['kind']);
        self::assertSame('example.io/disk', $storageClass['provisioner']);
        self::assertSame(['tier' => 'premium', 'fsType' => 'xfs'], $storageClass['parameters']);
        self::assertSame('Retain', $storageClass['reclaimPolicy']);
        self::assertSame('Immediate', $storageClass['volumeBindingMode']);
        self::assertFalse($storageClass['allowVolumeExpansion']);
        self::assertSame(['noatime', 'nodiratime'], $storageClass['mountOptions']);
        self::assertArrayNotHasKey('namespace', $storageClass['metadata'], 'A StorageClass is cluster-scoped');

        self::assertSame(
            [
                ['matchLabelExpressions' => [
                    ['key' => 'topology.kubernetes.io/zone', 'values' => ['eu-central-1a', 'eu-central-1b']],
                    ['key' => 'example.io/disk-tier', 'values' => ['premium']],
                ]],
                ['matchLabelExpressions' => [
                    ['key' => 'topology.kubernetes.io/zone', 'values' => ['eu-central-1c']],
                ]],
            ],
            $storageClass['allowedTopologies'],
        );

        $annotations = $storageClass['metadata']['annotations'];
        self::assertArrayNotHasKey('storageclass.kubernetes.io/is-default-class', $annotations, 'Not the default class unless isDefaultClass() says so');
        self::assertSame('storage', $annotations['klass8s.dev/app']);
        self::assertSame('storage', $storageClass['metadata']['labels']['app.kubernetes.io/name']);
        self::assertSame('fast', $storageClass['metadata']['labels']['app.kubernetes.io/component']);
    }

    public function testDefaults(): void
    {
        $storageClass = $this->findStorageClass('storage-plain');

        self::assertSame('example.io/plain', $storageClass['provisioner']);
        self::assertSame('Delete', $storageClass['reclaimPolicy']);
        self::assertSame('Immediate', $storageClass['volumeBindingMode']);
        self::assertFalse($storageClass['allowVolumeExpansion']);
        self::assertArrayNotHasKey('parameters', $storageClass);
        self::assertArrayNotHasKey('mountOptions', $storageClass);
        self::assertArrayNotHasKey('allowedTopologies', $storageClass);
        self::assertArrayNotHasKey('storageclass.kubernetes.io/is-default-class', $storageClass['metadata']['annotations']);
    }

    public function testEksAutoModeGp3Battery(): void
    {
        // Named `gp3` regardless of the app prefix — the conventional cluster name.
        $storageClass = $this->findStorageClass('gp3');

        self::assertSame('ebs.csi.eks.amazonaws.com', $storageClass['provisioner']);
        self::assertSame(['type' => 'gp3', 'encrypted' => 'true'], $storageClass['parameters']);
        self::assertSame('Delete', $storageClass['reclaimPolicy']);
        self::assertSame('WaitForFirstConsumer', $storageClass['volumeBindingMode']);
        self::assertTrue($storageClass['allowVolumeExpansion']);
        self::assertArrayNotHasKey('mountOptions', $storageClass);
        self::assertSame(
            [['matchLabelExpressions' => [['key' => 'eks.amazonaws.com/compute-type', 'values' => ['auto']]]]],
            $storageClass['allowedTopologies'],
        );

        $annotations = $storageClass['metadata']['annotations'];
        self::assertSame('true', $annotations['storageclass.kubernetes.io/is-default-class']);
        // The default-class annotation is the manifest's own: it precedes the framework's annotations.
        self::assertSame('storageclass.kubernetes.io/is-default-class', array_key_first($annotations));
        self::assertSame('storage', $annotations['klass8s.dev/app']);
        self::assertSame('gp3', $storageClass['metadata']['labels']['app.kubernetes.io/component']);
    }

    /**
     * @return array<string, mixed>
     */
    private function findStorageClass(string $name): array
    {
        $output = $this->appProcessor->process('storage');
        $yaml = $output->render()->toString();
        foreach ($this->parseYamlDocuments($yaml) as $document) {
            if ('StorageClass' === $document['kind'] && $name === ($document['metadata']['name'] ?? null)) {
                return $document;
            }
        }

        self::fail(sprintf('StorageClass "%s" not found in rendered output', $name));
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
}
