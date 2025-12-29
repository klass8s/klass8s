<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\ConfigMap\ConfigMapInterface;

class ConfigMapContext extends AbstractContext
{
    public function kind(): string
    {
        return 'ConfigMap';
    }

    public static function interfaceName(): string
    {
        return ConfigMapInterface::class;
    }
}
