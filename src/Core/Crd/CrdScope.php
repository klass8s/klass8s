<?php

declare(strict_types=1);

namespace Klass8s\Core\Crd;

enum CrdScope: string
{
    case Namespaced = 'Namespaced';
    case Cluster = 'Cluster';
}
