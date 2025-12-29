<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\CronJob\CronJobInterface;

class CronJobContext extends AbstractContext
{
    public function kind(): string
    {
        return 'CronJob';
    }

    public static function interfaceName(): string
    {
        return CronJobInterface::class;
    }
}
