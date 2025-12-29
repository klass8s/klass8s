<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\Service\ServiceInterface;

class ServiceContext extends AbstractContext
{
    public function kind(): string
    {
        return 'Service';
    }

    public static function interfaceName(): string
    {
        return ServiceInterface::class;
    }
}
