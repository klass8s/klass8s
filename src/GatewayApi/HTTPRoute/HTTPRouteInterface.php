<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute;

use Klass8s\GatewayApi\Configurator\ParentRefsConfigurator;
use Klass8s\GatewayApi\HTTPRoute\Configurator\HTTPRouteRulesConfigurator;
use Klass8s\Manifest\ManifestInterface;

interface HTTPRouteInterface extends ManifestInterface
{
    public function parentRefs(ParentRefsConfigurator $parentRefs): void;

    /**
     * @return list<string>
     */
    public function hostnames(): array;

    public function rules(HTTPRouteRulesConfigurator $rules): void;
}
