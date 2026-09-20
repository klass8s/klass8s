<?php

declare(strict_types=1);

namespace Klass8s\Tests\Checksum\Fixture\VolumeApp;

use K8Schema\Collection\StringMap;
use Klass8s\Core\ConfigMap\AbstractConfigMap;

/**
 * A ConfigMap the app renders itself and the deployment mounts as a volume. Its single value is a
 * constructor argument so a test can render the same project twice with different content.
 */
class MountedConfigMap extends AbstractConfigMap
{
    public const string KEY = 'app.conf';

    public function __construct(private readonly string $value)
    {
    }

    public static function shortName(): string
    {
        return 'mounted-config';
    }

    public function data(StringMap $data): void
    {
        $data->add(self::KEY, $this->value);
    }
}
