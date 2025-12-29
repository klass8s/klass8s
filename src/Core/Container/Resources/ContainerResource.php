<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Resources;

enum ContainerResource: string
{
    case CPU = 'cpu';
    case MEMORY = 'memory';
    case EPHEMERAL_STORAGE = 'ephemeral-storage';
    case STORAGE = 'storage';
}
