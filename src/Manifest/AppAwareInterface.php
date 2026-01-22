<?php

declare(strict_types=1);

namespace Klass8s\Manifest;

use Klass8s\App\AppInterface;

interface AppAwareInterface
{
    protected(set) AppInterface $app { get; set; }

    public function setApp(AppInterface $app): void;
}
