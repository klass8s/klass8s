<?php

declare(strict_types=1);

namespace Klass8s\Bundle\CodeGeneration\ManifestGenerator\Context;

use Klass8s\Core\Deployment\DeploymentInterface;

class DeploymentContext extends AbstractContext
{
    public function kind(): string
    {
        return 'Deployment';
    }

    public static function interfaceName(): string
    {
        return DeploymentInterface::class;
    }
}
