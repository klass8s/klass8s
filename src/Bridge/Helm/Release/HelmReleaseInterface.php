<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

interface HelmReleaseInterface
{
    public function name(): string;
    public function manifest(): string;
}
