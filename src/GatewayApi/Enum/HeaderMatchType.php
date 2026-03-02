<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum HeaderMatchType: string
{
    case Exact = 'Exact';
    case RegularExpression = 'RegularExpression';
}
