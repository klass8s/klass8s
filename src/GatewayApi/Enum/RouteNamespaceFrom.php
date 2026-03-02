<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum RouteNamespaceFrom: string
{
    case Same = 'Same';
    case All = 'All';
    case Selector = 'Selector';
}
