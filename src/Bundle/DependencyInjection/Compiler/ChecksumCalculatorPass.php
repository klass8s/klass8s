<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\Checksum\Calculator\ChecksumCalculatorInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class ChecksumCalculatorPass implements CompilerPassInterface
{
    public const string CHECKSUM_CALCULATOR_TAG = 'klass8s.checksum_calculator';

    public function process(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ChecksumCalculatorInterface::class)
            ->addTag(self::CHECKSUM_CALCULATOR_TAG);
    }
}
