<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bundle\Autoscaling\Fixture\AutoscaleApp;

use Klass8s\App\AbstractApp;

class AutoscaleApp extends AbstractApp
{
    public static function name(): string
    {
        return 'autoscale';
    }
}
