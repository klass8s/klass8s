<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\Ingress\IngressInterface;

class IngressContext extends AbstractContext
{
    public function kind(): string
    {
        return 'Ingress';
    }

    public static function interfaceName(): string
    {
        return IngressInterface::class;
    }
}
