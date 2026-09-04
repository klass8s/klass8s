<?php

declare(strict_types=1);

namespace Klass8s\Tests\Storage\Fixture\StorageApp;

use Klass8s\App\AbstractApp;

class StorageApp extends AbstractApp
{
    public static function name(): string
    {
        return 'storage';
    }
}
