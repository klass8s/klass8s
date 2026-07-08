<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Runtime;

use Klass8s\Bundle\EnvManagement\Attribute\EnabledForEnvs;
use Klass8s\Bundle\EnvManagement\Env;
use Klass8s\Runtime\DelegatingRuntimeStatusResolver;
use Klass8s\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Guards the tagged-iterator wiring between the EnvManagement strategies and
 * DelegatingRuntimeStatusResolver. If the tag names drift apart, the resolver runs with
 * zero strategies and every EnvManagement attribute silently becomes a no-op.
 */
class StatusResolverWiringTest extends KernelTestCase
{
    public function testEnvGatedClassIsDisabledOutsideItsEnvs(): void
    {
        self::bootKernel();
        $resolver = self::getContainer()->get(DelegatingRuntimeStatusResolver::class);

        self::assertFalse(
            $resolver->isClassEnabled(new \ReflectionClass(ProductionOnlyFixture::class)),
            'A production-only class must be disabled in the test env. If it is enabled, the resolver has no strategies wired.'
        );
        self::assertTrue($resolver->isClassEnabled(new \ReflectionClass(TestEnvOnlyFixture::class)));
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}

#[EnabledForEnvs(Env::PRODUCTION)]
class ProductionOnlyFixture
{
}

#[EnabledForEnvs(Env::TEST)]
class TestEnvOnlyFixture
{
}
