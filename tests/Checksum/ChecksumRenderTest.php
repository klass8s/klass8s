<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum;

use Klass8s\App\AppManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Regression test: checksum annotations are written by a listener on ManifestsProcessedEvent,
 * AFTER all manifests are processed. They only reach the output if OutputDirectory renders
 * lazily (at render()/dumpTo() time). With eager rendering in addFile(), the annotations are
 * silently lost — which is exactly what this test guards against.
 */
class ChecksumRenderTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return ChecksumTestKernel::class;
    }

    public function testChecksumAnnotationsReachRenderedOutput(): void
    {
        self::bootKernel();
        /** @var AppManager $appManager */
        $appManager = self::getContainer()->get(AppManager::class);

        $yaml = $appManager->generateManifests('checksum')->render()->toString();
        $documents = $this->parseYamlDocuments($yaml);

        $deployment = $this->findDocument($documents, 'Deployment');
        self::assertNotNull($deployment, 'Deployment document not found in rendered output');

        $annotations = $deployment['spec']['template']['metadata']['annotations'] ?? [];
        self::assertArrayHasKey(
            'klass8s.dev/env-sources-checksum',
            $annotations,
            'env-sources checksum annotation is missing — mutations made on ManifestsProcessedEvent did not reach the rendered output',
        );
        self::assertNotEmpty($annotations['klass8s.dev/env-sources-checksum']);
    }

    /**
     * @param list<array<string, mixed>> $documents
     *
     * @return array<string, mixed>|null
     */
    private function findDocument(array $documents, string $kind): ?array
    {
        foreach ($documents as $document) {
            if ($kind === $document['kind']) {
                return $document;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseYamlDocuments(string $yaml): array
    {
        $documents = [];
        foreach (preg_split('/\n---\n/', $yaml) as $document) {
            $document = trim($document);
            if ('' === $document) {
                continue;
            }
            $document = preg_replace('/^#\s*File:.*\n/', '', $document);
            $parsed = Yaml::parse($document);
            if (is_array($parsed) && isset($parsed['kind'])) {
                $documents[] = $parsed;
            }
        }

        return $documents;
    }
}
