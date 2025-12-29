<?php

declare(strict_types=1);

namespace Klass8s\Core;

interface DynamicNameAwareInterface
{
    public function name(): string;
}
