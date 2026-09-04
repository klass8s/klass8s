<?php

declare(strict_types=1);

namespace Klass8s\Tests\Storage\Fixture\StorageApp;

use Klass8s\Core\Persistence\AbstractStorageClass;

/**
 * Nothing overridden but the required provisioner — proves the defaults.
 */
class TestMinimalStorageClass extends AbstractStorageClass
{
    public static function shortName(): string
    {
        return 'plain';
    }

    public function provisioner(): string
    {
        return 'example.io/plain';
    }
}
