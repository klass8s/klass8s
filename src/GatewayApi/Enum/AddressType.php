<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum AddressType: string
{
    case IPAddress = 'IPAddress';
    case Hostname = 'Hostname';
    case NamedAddress = 'NamedAddress';
}
