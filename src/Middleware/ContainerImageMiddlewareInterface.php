<?php

declare(strict_types=1);

namespace Klass8s\Middleware;

use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Image\Image;

interface ContainerImageMiddlewareInterface
{
    public function apply(Image $image, AppInterface $app): void;
    public static function priority(): int;
}
