<?php

declare(strict_types=1);

namespace Klass8s\Core;

use Klass8s\App\AppInterface;

interface AppAwareInterface
{
    public function setApp(AppInterface $app): void;
}
