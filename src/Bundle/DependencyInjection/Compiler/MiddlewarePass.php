<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Middleware\ContainerImageMiddlewareInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class MiddlewarePass implements CompilerPassInterface
{
    public const string IMAGE_MIDDLEWARE_TAG = 'klass8s.middleware.container_image';

    public function process(ContainerBuilder $container): void
    {
        $container
            ->registerForAutoconfiguration(ContainerImageMiddlewareInterface::class)
            ->addTag(self::IMAGE_MIDDLEWARE_TAG);
    }
}
