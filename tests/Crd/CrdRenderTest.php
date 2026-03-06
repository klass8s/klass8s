<?php

declare(strict_types=1);

namespace Klass8s\Tests\Crd;

use Klass8s\App\AppProcessor;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

class CrdRenderTest extends KernelTestCase
{
    private AppProcessor $appProcessor;

    protected static function getKernelClass(): string
    {
        return CrdTestKernel::class;
    }

    protected function setUp(): void
    {
        self::bootKernel();
        $this->appProcessor = self::getContainer()->get(AppProcessor::class);
    }

    public function testCrdRendering(): void
    {
        $output = $this->appProcessor->process('crd');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $crd = $this->findDocument($documents, 'CustomResourceDefinition');
        self::assertNotNull($crd, 'CustomResourceDefinition document not found in rendered output');

        // apiVersion & kind
        self::assertSame('apiextensions.k8s.io/v1', $crd['apiVersion']);
        self::assertSame('CustomResourceDefinition', $crd['kind']);

        // CRD name must be {plural}.{group}
        self::assertSame('certificates.cert-manager.io', $crd['metadata']['name']);

        // Spec top-level fields
        self::assertSame('cert-manager.io', $crd['spec']['group']);
        self::assertSame('Namespaced', $crd['spec']['scope']);

        // Names
        $names = $crd['spec']['names'];
        self::assertSame('Certificate', $names['kind']);
        self::assertSame('certificates', $names['plural']);
        self::assertSame('certificate', $names['singular']);
        self::assertSame('CertificateList', $names['listKind']);
        self::assertSame(['cert', 'certs'], $names['shortNames']);
        self::assertSame(['cert-manager'], $names['categories']);
    }

    public function testCrdVersions(): void
    {
        $output = $this->appProcessor->process('crd');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);
        $crd = $this->findDocument($documents, 'CustomResourceDefinition');

        $versions = $crd['spec']['versions'];
        self::assertCount(2, $versions);

        // v1 — served and storage
        $v1 = $versions[0];
        self::assertSame('v1', $v1['name']);
        self::assertTrue($v1['served']);
        self::assertTrue($v1['storage']);

        // OpenAPI schema
        self::assertArrayHasKey('openAPIV3Schema', $v1['schema']);
        $schema = $v1['schema']['openAPIV3Schema'];
        self::assertSame('object', $schema['type']);
        self::assertArrayHasKey('spec', $schema['properties']);
        self::assertArrayHasKey('status', $schema['properties']);
        self::assertSame(['secretName', 'issuerRef'], $schema['properties']['spec']['required']);

        // Printer columns
        self::assertCount(3, $v1['additionalPrinterColumns']);

        $readyCol = $v1['additionalPrinterColumns'][0];
        self::assertSame('Ready', $readyCol['name']);
        self::assertSame('boolean', $readyCol['type']);
        self::assertSame('.status.ready', $readyCol['jsonPath']);
        self::assertSame('Whether the certificate is ready', $readyCol['description']);

        $secretCol = $v1['additionalPrinterColumns'][1];
        self::assertSame('Secret', $secretCol['name']);
        self::assertSame('string', $secretCol['type']);
        self::assertSame('.spec.secretName', $secretCol['jsonPath']);

        $expirationCol = $v1['additionalPrinterColumns'][2];
        self::assertSame('Expiration', $expirationCol['name']);
        self::assertSame('date', $expirationCol['type']);
        self::assertSame(1, $expirationCol['priority']);

        // Subresources — status enabled
        self::assertArrayHasKey('subresources', $v1);
        self::assertArrayHasKey('status', $v1['subresources']);

        // Selectable fields
        self::assertCount(1, $v1['selectableFields']);
        self::assertSame('.spec.issuerRef.name', $v1['selectableFields'][0]['jsonPath']);
    }

    public function testCrdDeprecatedVersion(): void
    {
        $output = $this->appProcessor->process('crd');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);
        $crd = $this->findDocument($documents, 'CustomResourceDefinition');

        $v1alpha1 = $crd['spec']['versions'][1];
        self::assertSame('v1alpha1', $v1alpha1['name']);
        self::assertTrue($v1alpha1['served']);
        self::assertFalse($v1alpha1['storage']);
        self::assertTrue($v1alpha1['deprecated']);
        self::assertSame(
            'cert-manager.io/v1alpha1 Certificate is deprecated; use v1',
            $v1alpha1['deprecationWarning'],
        );
    }

    public function testCrdLabels(): void
    {
        $output = $this->appProcessor->process('crd');
        $yaml = $output->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);
        $crd = $this->findDocument($documents, 'CustomResourceDefinition');

        $labels = $crd['metadata']['labels'] ?? [];
        self::assertSame('crd', $labels['app.kubernetes.io/name']);
        self::assertSame('certificates', $labels['app.kubernetes.io/component']);
        self::assertSame('cert-manager', $labels['app.kubernetes.io/part-of']);

        $annotations = $crd['metadata']['annotations'] ?? [];
        self::assertSame('crd', $annotations['klass8s.dev/app']);
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

    private function findDocument(array $documents, string $kind): ?array
    {
        foreach ($documents as $doc) {
            if (($doc['kind'] ?? null) === $kind) {
                return $doc;
            }
        }

        return null;
    }
}
