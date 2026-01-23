<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart\Reference;

use Klass8s\Bridge\Helm\Chart\ChartReferenceType;
use Klass8s\Bridge\Helm\Chart\UnpackedChartDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

final readonly class ChartLocalCopyProvider
{
    public function __construct(private string $projectDir, private string $cacheDir)
    {
    }

    public function createFrom(ChartReference $ref): UnpackedChartDirectory
    {
        return match ($ref->type) {
            ChartReferenceType::ChartName,
            ChartReferenceType::Url,
            ChartReferenceType::OciRegistry => $this->pullChart($ref),
            ChartReferenceType::RelativePath => new UnpackedChartDirectory($this->projectDir.'/'.ltrim($ref->value, './\\')),
            ChartReferenceType::AbsolutePath => new UnpackedChartDirectory($ref->value),
            default => throw new \InvalidArgumentException(sprintf('Chart reference of type "%s" is not supported.', $ref->type->name)),
        };
    }

    private function pullChart(ChartReference $ref): UnpackedChartDirectory
    {
        $fs = new Filesystem();

        $chartHash = md5($ref->value.'@'.($ref->version ?? 'latest').'_'.date('Y-m-d'));
        $cachedPath = $this->cacheDir.'/helm/'.$chartHash;

        if ($fs->exists($cachedPath)) {
            return new UnpackedChartDirectory($cachedPath);
        }

        $fs->mkdir($cachedPath);

        $command = ['helm', 'pull', $ref->value, '--untar', '--untardir', $cachedPath];
        $process = new Process($command, cwd: $this->projectDir, timeout: 300);
        $process->mustRun();

        $cachedPathContents = scandir($cachedPath);
        if (false === $cachedPathContents) {
            throw new \RuntimeException(sprintf('Failed to scan chart directory "%s".', $cachedPath));
        }

        $cachedPathContents = array_filter($cachedPathContents, fn (string $item) => !str_starts_with($item, '.'));
        if (count($cachedPathContents) > 1) {
            throw new \LogicException(sprintf('Chart directory "%s" must contain exactly one directory for unpacked chart, but contains multiple files / directories.', $cachedPath));
        }

        $nestedChartPath = $cachedPath.'/'.array_first($cachedPathContents);

        $fs->mirror($nestedChartPath, $cachedPath);
        $fs->remove($nestedChartPath);

        return new UnpackedChartDirectory($cachedPath);
    }
}
