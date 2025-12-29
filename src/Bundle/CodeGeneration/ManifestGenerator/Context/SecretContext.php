<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\Secret\SecretInterface;

class SecretContext extends AbstractContext
{
    public function kind(): string
    {
        return 'Secret';
    }

    public static function interfaceName(): string
    {
        return SecretInterface::class;
    }
}
