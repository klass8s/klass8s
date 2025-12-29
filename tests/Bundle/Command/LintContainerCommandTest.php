<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Command;

use Klass8s\Tests\TestKernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * A simple test that DI container is valid.
 */
class LintContainerCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        // @phpstan-ignore argument.type
        $application = new Application(self::$kernel);
        $command = $application->find('lint:container');

        $commandTester = new CommandTester($command);
        $commandTester->execute([], ['capture_stderr_separately' => true]);
        $commandTester->assertCommandIsSuccessful($commandTester->getErrorOutput());
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
