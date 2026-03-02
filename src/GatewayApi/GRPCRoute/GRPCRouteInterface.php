<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\GRPCRoute;

use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\GRPCRoute\Configurator\GRPCRouteRulesConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface GRPCRouteInterface extends ManifestInterface
{
    public function parentRefs(ParentRefsConfigurator $parentRefs): void;

    /**
     * @return list<string>
     */
    public function hostnames(): array;

    public function rules(GRPCRouteRulesConfigurator $rules): void;
}
