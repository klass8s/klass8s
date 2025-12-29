<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\Job\JobInterface;

class JobContext extends AbstractContext
{
    public function kind(): string
    {
        return 'Job';
    }

    public static function interfaceName(): string
    {
        return JobInterface::class;
    }
}
