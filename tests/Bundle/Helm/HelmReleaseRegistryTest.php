<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Helm;

use Klass8s\Bridge\Helm\Release\HelmRelease;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Bundle\EnvManagement\Attribute\EnabledForConfigurationParameter;
use Klass8s\Bundle\Runtime\StatusResolver\Strategy\EnabledForConfigurationParameterStrategy;
use Klass8s\Runtime\DelegatingRuntimeStatusResolver;
use Klass8s\Runtime\ManifestRuntimeStatusResolverInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class HelmReleaseRegistryTest extends TestCase
{
    public function testEnabledReleaseIsRegistered(): void
    {
        $registry = new HelmReleaseRegistry($this->allowAllResolver());
        $registry->add($this->makeRelease('valkey'));

        self::assertTrue($registry->has('valkey'));
        self::assertSame(['valkey'], $registry->releaseNames);
    }

    public function testDisabledReleaseIsNotRegistered(): void
    {
        $registry = new HelmReleaseRegistry($this->allowAllResolver());
        $registry->add($this->makeRelease('valkey', enabled: false));

        self::assertFalse($registry->has('valkey'));
        self::assertSame([], $registry->releaseNames);
    }

    public function testReleaseClassDisabledByConfigurationParameterIsNotRegistered(): void
    {
        $registry = new HelmReleaseRegistry($this->parameterResolver(['tests.helm.release_enabled' => false]));
        $registry->add(new ParameterGatedHelmRelease(
            name: 'mongodb-operator',
            values: [],
            valuesProvider: null,
            deployPriority: 0,
            chartReference: 'mongodb/mongodb-kubernetes',
        ));

        self::assertFalse($registry->has('mongodb-operator'));
    }

    public function testReleaseClassEnabledByConfigurationParameterIsRegistered(): void
    {
        $registry = new HelmReleaseRegistry($this->parameterResolver(['tests.helm.release_enabled' => true]));
        $registry->add(new ParameterGatedHelmRelease(
            name: 'mongodb-operator',
            values: [],
            valuesProvider: null,
            deployPriority: 0,
            chartReference: 'mongodb/mongodb-kubernetes',
        ));

        self::assertTrue($registry->has('mongodb-operator'));
    }

    private function makeRelease(string $name, bool $enabled = true): HelmRelease
    {
        return new HelmRelease(
            name: $name,
            values: [],
            valuesProvider: null,
            deployPriority: 0,
            chartReference: 'example/chart',
            enabled: $enabled,
        );
    }

    private function allowAllResolver(): ManifestRuntimeStatusResolverInterface
    {
        return new DelegatingRuntimeStatusResolver([]);
    }

    /**
     * @param array<string, bool> $parameters
     */
    private function parameterResolver(array $parameters): ManifestRuntimeStatusResolverInterface
    {
        return new DelegatingRuntimeStatusResolver([
            new EnabledForConfigurationParameterStrategy(new ParameterBag($parameters)),
        ]);
    }
}

#[EnabledForConfigurationParameter('tests.helm.release_enabled')]
readonly class ParameterGatedHelmRelease extends HelmRelease
{
}
