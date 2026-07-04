<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\ChecksumApp;

use Klass8s\App\AbstractApp;

class ChecksumApp extends AbstractApp
{
    public static function name(): string
    {
        return 'checksum';
    }
}
