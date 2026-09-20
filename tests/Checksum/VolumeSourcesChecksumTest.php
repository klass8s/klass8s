<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum;

use Klass8s\App\AppManager;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * The trap this closes: a ConfigMap's content changes, the pod template does not, and Kubernetes
 * rolls nothing — the pods keep the old config until somebody runs `kubectl rollout restart`.
 * Klass8s writes the mounted sources' fingerprint into the pod template so the change IS a pod
 * template change.
 *
 * The second half is the reason the feature was switched off across the platform (iam 3dd6010,
 * composer 2f92abd, July 2026): a volume from a Secret the project does not render — the target of
 * an ExternalSecret — made the whole render throw. It is now declarable as `external: true`, and
 * the checksum leaves it out because there are no bytes to fingerprint.
 */
final class VolumeSourcesChecksumTest extends TestCase
{
    private const string ANNOTATION_VOLUMES = 'klass8s.dev/volume-sources-checksum';
    private const string ANNOTATION_ENV = 'klass8s.dev/env-sources-checksum';

    #[Test]
    public function aDeploymentMountingARenderedConfigMapCarriesTheVolumeSourcesChecksum(): void
    {
        $annotations = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info'));

        self::assertArrayHasKey(self::ANNOTATION_VOLUMES, $annotations);
        self::assertNotEmpty($annotations[self::ANNOTATION_VOLUMES]);
    }

    #[Test]
    public function theChecksumMovesWhenTheMountedConfigMapContentChanges(): void
    {
        $before = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info'));
        $after = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = debug'));

        self::assertNotSame(
            $before[self::ANNOTATION_VOLUMES],
            $after[self::ANNOTATION_VOLUMES],
            'A mounted ConfigMap changed and the pod template did not — the workload would never roll.',
        );
    }

    #[Test]
    public function theChecksumIsStableWhenNothingChanges(): void
    {
        $first = $this->render(new VolumeChecksumTestKernel('log_level = info', cacheSalt: 'first'));
        $second = $this->render(new VolumeChecksumTestKernel('log_level = info', cacheSalt: 'second'));

        // Byte-identical, not merely equal-checksummed: the gitops `validate` workflow re-renders
        // and diffs, so any per-run entropy in the annotation would fail every validate.
        self::assertSame($first, $second, 'Two renders of the same input are not byte-identical.');
    }

    #[Test]
    public function anExternalVolumeSourceIsMountedWithoutFailingTheRender(): void
    {
        // The Secret this deployment mounts at /etc/tls is written by ESO and rendered by nobody.
        // Before the fix this threw — which is why every service turned checksums off.
        $deployment = $this->deployment($this->render(new VolumeChecksumTestKernel('log_level = info')));

        $mountedSecrets = array_filter(array_map(
            static fn (array $volume): ?string => $volume['secret']['secretName'] ?? null,
            $deployment['spec']['template']['spec']['volumes'],
        ));

        self::assertSame(
            [VolumeChecksumTestKernel::DEFAULT_ESO_SECRET_NAME],
            array_values($mountedSecrets),
            'The fixture no longer mounts the ESO-written Secret — the regression it guards is untested.',
        );
        self::assertArrayHasKey(
            self::ANNOTATION_VOLUMES,
            $deployment['spec']['template']['metadata']['annotations'],
        );
    }

    #[Test]
    public function anExternalVolumeSourceContributesNothingToTheChecksum(): void
    {
        // Its bytes are unknown at render time, so it cannot be fingerprinted. Renaming it still
        // rolls the workload — the name is in the pod template's own `volumes:` — but through the
        // spec, not through a checksum that would be pretending to know something.
        $before = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info'));
        $after = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info', esoSecretName: 'volumes-tls-elsewhere'));

        self::assertSame($before[self::ANNOTATION_VOLUMES], $after[self::ANNOTATION_VOLUMES]);
    }

    #[Test]
    public function anUnmarkedUnrenderedVolumeSourceStillFailsTheRenderAndNamesTheFix(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessageMatches('/external: true/');

        $this->render(new VolumeChecksumTestKernel('log_level = info', markSecretExternal: false));
    }

    #[Test]
    public function anInitContainersEnvSourceCountsTowardsTheEnvChecksum(): void
    {
        $before = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info', initConfigValue: 'migrate'));
        $after = $this->podTemplateAnnotations(new VolumeChecksumTestKernel('log_level = info', initConfigValue: 'seed'));

        self::assertNotSame(
            $before[self::ANNOTATION_ENV],
            $after[self::ANNOTATION_ENV],
            'Only the init container reads that ConfigMap — and it rides in the same pod template.',
        );
    }

    /**
     * @return array<string, string>
     */
    private function podTemplateAnnotations(VolumeChecksumTestKernel $kernel): array
    {
        return $this->deployment($this->render($kernel))['spec']['template']['metadata']['annotations'] ?? [];
    }

    private function render(VolumeChecksumTestKernel $kernel): string
    {
        $kernel->boot();

        try {
            /** @var AppManager $appManager */
            $appManager = $kernel->getContainer()->get('test.service_container')->get(AppManager::class);

            return $appManager->generateManifests('volumes')->render()->toString();
        } finally {
            $kernel->shutdown();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function deployment(string $yaml): array
    {
        foreach (preg_split('/\n---\n/', $yaml) as $document) {
            $document = preg_replace('/^#\s*File:.*\n/', '', trim($document));
            if ('' === $document) {
                continue;
            }

            $parsed = Yaml::parse($document);
            if (is_array($parsed) && 'Deployment' === ($parsed['kind'] ?? null)) {
                return $parsed;
            }
        }

        self::fail('No Deployment document in the rendered output.');
    }
}
