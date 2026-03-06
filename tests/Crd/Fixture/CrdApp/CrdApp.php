<?php

declare(strict_types=1);

namespace Klass8s\Tests\Crd\Fixture\CrdApp;

use Klass8s\App\AbstractApp;

class CrdApp extends AbstractApp
{
    public static function name(): string
    {
        return 'crd';
    }
}
