<?php

declare(strict_types=1);

namespace Klass8s\Core\Service;

enum IPProtocol: string
{
    case TCP = 'TCP';
    case UDP = 'UDP';
    case SCTP = 'SCTP';

    public function toString(): string
    {
        return $this->value;
    }
}
