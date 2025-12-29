<?php

declare(strict_types=1);

namespace Klass8s\Core\Ingress\Http;

enum PathType: string
{
    case Exact = 'Exact';
    case Prefix = 'Prefix';
    case ImplementationSpecific = 'ImplementationSpecific';

    public function toString(): string
    {
        return $this->value;
    }
}
