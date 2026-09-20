<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\VolumeApp;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

/**
 * A ConfigMap that ONLY the init container consumes — nothing in the main container references it.
 */
class InitConfigMap extends AbstractConfigMap
{
    public const string KEY = 'INIT_MODE';

    public function __construct(private readonly string $value)
    {
    }

    public static function shortName(): string
    {
        return 'init-config';
    }

    public function data(StringMap $data): void
    {
        $data->add(self::KEY, $this->value);
    }
}
