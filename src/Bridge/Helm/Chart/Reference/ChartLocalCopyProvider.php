<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Chart\Reference;

use Klass8s\Bridge\Helm\Chart\ChartCacheDir;
use Klass8s\Bridge\Helm\Chart\ChartReferenceType;
use Klass8s\Bridge\Helm\Chart\UnpackedChartDirectory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * A chart reference becomes a directory `helm template` can read: a path as it is,
 * a pulled chart from the cache ({@see ChartCacheDir}), pulled once and shared —
 * a pinned version is immutable, so its directory is keyed by chart and version
 * alone; `latest` is keyed by the hour, so a render is never more than an hour
 * behind. Two renders may pull the same chart at once (a fan-out renders products
 * concurrently): each pulls into a private directory and moves it into place, and
 * the one that finds the place taken keeps what is there.
 */
final readonly class ChartLocalCopyProvider
{
    public function __construct(
        private string $projectDir,
        private string $cacheDir,
        private ?string $chartCacheDir = null,
    ) {
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

    /** The directory a chart at a version unpacks into — stable for a pinned version, hourly for `latest`. */
    public static function cachePath(string $chartCacheDir, string $chart, ?string $version): string
    {
        $chartVersion = $version ?? 'latest';
        $key = null === $version ? $chart.'@latest_'.date('Y-m-d H') : $chart.'@'.$chartVersion;

        return rtrim($chartCacheDir, '/').'/'.md5($key);
    }

    private function pullChart(ChartReference $ref): UnpackedChartDirectory
    {
        $fs = new Filesystem();
        $chartCacheDir = ChartCacheDir::resolve($this->chartCacheDir, $this->cacheDir);
        $cachedPath = self::cachePath($chartCacheDir, $ref->value, $ref->version);

        if ($fs->exists($cachedPath.'/Chart.yaml')) {
            return new UnpackedChartDirectory($cachedPath);
        }

        $fs->mkdir($chartCacheDir);
        $pullDir = $chartCacheDir.'/.pull-'.basename($cachedPath).'-'.bin2hex(random_bytes(4));
        try {
            $command = ['helm', 'pull', $ref->value, '--untar', '--untardir', $pullDir, '--version', $ref->version ?? 'latest'];
            $process = new Process($command, cwd: $this->projectDir, timeout: 300);
            $process->mustRun();

            $contents = scandir($pullDir);
            if (false === $contents) {
                throw new \RuntimeException(sprintf('Failed to scan chart directory "%s".', $pullDir));
            }
            $contents = array_values(array_filter($contents, static fn (string $item): bool => !str_starts_with($item, '.')));
            if (1 !== count($contents)) {
                throw new \LogicException(sprintf('Chart directory "%s" must contain exactly one directory for the unpacked chart, but contains %d entries.', $pullDir, count($contents)));
            }

            // Into place in one step: a render that finds Chart.yaml never sees a half-unpacked chart.
            if ($fs->exists($cachedPath.'/Chart.yaml') || !@rename($pullDir.'/'.$contents[0], $cachedPath)) {
                if (!$fs->exists($cachedPath.'/Chart.yaml')) {
                    throw new \RuntimeException(sprintf('Could not move the pulled chart into "%s".', $cachedPath));
                }
                // Another render pulled the same chart meanwhile: theirs is as good as ours.
            }
        } finally {
            $fs->remove($pullDir);
        }

        return new UnpackedChartDirectory($cachedPath);
    }
}
