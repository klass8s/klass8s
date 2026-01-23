<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Cli;

use Symfony\Component\Process\Process;

final readonly class HelmCli
{
    public function repoAdd(string $name, string $url): void
    {
        $process = new Process(['helm', 'repo', 'add', $name, $url], timeout: 60);
        $process->mustRun();
    }
}
