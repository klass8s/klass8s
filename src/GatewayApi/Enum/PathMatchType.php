<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\Enum;

enum PathMatchType: string
{
    case Exact = 'Exact';
    case PathPrefix = 'PathPrefix';
    case RegularExpression = 'RegularExpression';
}
