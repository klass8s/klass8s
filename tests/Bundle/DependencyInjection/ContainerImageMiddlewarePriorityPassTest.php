<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\DependencyInjection;

use Klass8s\App\AppInterface;
use Klass8s\Bundle\DependencyInjection\Compiler\ContainerImageMiddlewarePriorityPass;
use Klass8s\Bundle\Klass8sBundle;
use Klass8s\Core\Container\Image\Image;
use Klass8s\Middleware\ContainerImageMiddlewareInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class ContainerImageMiddlewarePriorityPassTest extends TestCase
{
    public function testThePriorityMethodBecomesTheTagAttribute(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('mw', (new Definition(HighPriorityMiddleware::class))->addTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE));
        $container->setDefinition('explicit', (new Definition(HighPriorityMiddleware::class))->addTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE, ['priority' => -5]));
        $container->setDefinition('foreign', (new Definition(\stdClass::class))->addTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE));

        (new ContainerImageMiddlewarePriorityPass())->process($container);

        self::assertSame([['priority' => 40]], $container->getDefinition('mw')->getTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE));
        self::assertSame([['priority' => -5]], $container->getDefinition('explicit')->getTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE), 'an explicit attribute wins');
        self::assertSame([[]], $container->getDefinition('foreign')->getTag(Klass8sBundle::TAG_IMAGE_MIDDLEWARE), 'a service that is not a middleware is left alone');
    }
}

final class HighPriorityMiddleware implements ContainerImageMiddlewareInterface
{
    public function apply(Image $image, AppInterface $app): void
    {
    }

    public static function priority(): int
    {
        return 40;
    }
}
