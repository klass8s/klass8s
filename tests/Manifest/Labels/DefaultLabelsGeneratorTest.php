<?php

declare(strict_types=1);

namespace Klass8s\Tests\Manifest\Labels;

use Klass8s\App\AppInterface;
use Klass8s\Manifest\AbstractSelfMakingManifest;
use Klass8s\Manifest\FullNameAwareInterface;
use Klass8s\Manifest\Labels\DefaultLabelsGenerator;
use Klass8s\Manifest\ManifestTemplateInterface;
use Klass8s\Manifest\ManifestTemplateTrait;
use PHPUnit\Framework\TestCase;

/**
 * The component label is the manifest's short name in every shape a manifest
 * comes in — the static one, a full-name-aware one, a template's dynamic one,
 * and a template that is ALSO full-name aware (a data-driven set whose instances
 * carry fixed names: one runner set per GitHub org, named after the org).
 */
final class DefaultLabelsGeneratorTest extends TestCase
{
    private function app(): AppInterface
    {
        $app = $this->createStub(AppInterface::class);
        $app->method('alias')->willReturn('plane');
        $app->method('resourceNameFromShortName')->willReturnCallback(static fn (string $short): string => 'plane-'.$short);

        return $app;
    }

    public function testAStaticallyNamedManifest(): void
    {
        $manifest = new class extends AbstractSelfMakingManifest {
            public static function shortName(): string
            {
                return 'server';
            }

            public static function apiVersion(): string
            {
                return 'v1';
            }

            public static function kind(): string
            {
                return 'ConfigMap';
            }

            public function spec(): array
            {
                return [];
            }
        };
        $manifest->setApp($this->app());

        self::assertSame(['app.kubernetes.io/name' => 'plane', 'app.kubernetes.io/component' => 'server'], new DefaultLabelsGenerator()->forManifest($manifest));
    }

    public function testATemplateUsesItsDynamicShortName(): void
    {
        $manifest = new class('acme') extends AbstractSelfMakingManifest implements ManifestTemplateInterface {
            use ManifestTemplateTrait;

            public function __construct(string $name)
            {
                $this->dynamicShortName = 'set-'.$name;
            }

            public static function apiVersion(): string
            {
                return 'v1';
            }

            public static function kind(): string
            {
                return 'ConfigMap';
            }

            public function spec(): array
            {
                return [];
            }
        };
        $manifest->setApp($this->app());

        self::assertSame('set-acme', new DefaultLabelsGenerator()->forManifest($manifest)['app.kubernetes.io/component']);
    }

    public function testAFullNameAwareTemplateUsesItsDynamicShortNameNotTheStaticOne(): void
    {
        $manifest = new class('acme') extends AbstractSelfMakingManifest implements ManifestTemplateInterface, FullNameAwareInterface {
            use ManifestTemplateTrait;

            public function __construct(private readonly string $org)
            {
                $this->dynamicShortName = $org;
            }

            public function fullName(): string
            {
                return $this->org;
            }

            public static function apiVersion(): string
            {
                return 'actions.github.com/v1alpha1';
            }

            public static function kind(): string
            {
                return 'AutoscalingRunnerSet';
            }

            public function spec(): array
            {
                return [];
            }
        };
        $manifest->setApp($this->app());

        // Before the fix this reached the trait's static shortName(), which throws by design.
        self::assertSame(['app.kubernetes.io/name' => 'plane', 'app.kubernetes.io/component' => 'acme'], new DefaultLabelsGenerator()->forManifest($manifest));
        self::assertSame('acme', $manifest->computedFullName, 'the full name names the resource');
    }
}
