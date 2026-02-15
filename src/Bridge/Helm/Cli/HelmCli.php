<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Cli;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final readonly class HelmCli
{
    public function repoAdd(string $name, string $url): void
    {
        $process = new Process(['helm', 'repo', 'add', $name, $url], timeout: 60);
        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            if (str_contains($e->getMessage(), 'already exists')) {
                // Ignore the error if the repository already exists
                return;
            }

            throw $e;
        }
    }
}
