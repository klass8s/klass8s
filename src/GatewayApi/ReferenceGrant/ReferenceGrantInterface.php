<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\ReferenceGrant;

use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantFromConfigurator;
use Klass8s\GatewayApi\ReferenceGrant\Configurator\ReferenceGrantToConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface ReferenceGrantInterface extends ManifestInterface
{
    public function from(ReferenceGrantFromConfigurator $from): void;

    public function to(ReferenceGrantToConfigurator $to): void;
}
