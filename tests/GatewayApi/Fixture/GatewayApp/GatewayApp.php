<?php

declare(strict_types=1);

namespace Klass8s\Tests\GatewayApi\Fixture\GatewayApp;

use Klass8s\App\AbstractApp;

class GatewayApp extends AbstractApp
{
    public static function name(): string
    {
        return 'gateway';
    }
}
