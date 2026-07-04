<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\ChecksumApp;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

class TestConfigMap extends AbstractConfigMap
{
    public static function shortName(): string
    {
        return 'config';
    }

    public function data(StringMap $data): void
    {
        $data->add('LOG_LEVEL', 'info');
        $data->add('CACHE_HOST', 'valkey.local');
    }
}
