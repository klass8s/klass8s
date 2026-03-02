<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum TlsMode: string
{
    case Terminate = 'Terminate';
    case Passthrough = 'Passthrough';
}
