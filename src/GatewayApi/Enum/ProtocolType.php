<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum ProtocolType: string
{
    case HTTP = 'HTTP';
    case HTTPS = 'HTTPS';
    case TLS = 'TLS';
    case TCP = 'TCP';
    case UDP = 'UDP';
}
