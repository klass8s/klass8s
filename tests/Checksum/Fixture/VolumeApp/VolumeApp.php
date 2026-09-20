<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\VolumeApp;

use Klass8s\App\AbstractApp;

class VolumeApp extends AbstractApp
{
    public static function name(): string
    {
        return 'volumes';
    }
}
