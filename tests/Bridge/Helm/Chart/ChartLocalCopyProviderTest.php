<?php

declare(strict_types=1);

namespace Klass8s\Tests\Bridge\Helm\Chart;

use Klass8s\Bridge\Helm\Chart\Reference\ChartLocalCopyProvider;
use Klass8s\Bridge\Helm\Chart\Reference\ChartReference;
use PHPUnit\Framework\TestCase;

/** Pulled charts are pulled once into the shared cache and moved into place whole — a fake `helm` on PATH records every pull. */
class ChartLocalCopyProviderTest extends TestCase
{
    private string $tmp;
    private string|false $path = false;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir().'/klass8s-chart-pull-'.bin2hex(random_bytes(4));
        mkdir($this->tmp.'/bin', 0o755, true);
        mkdir($this->tmp.'/project');
        // helm pull <chart> --untar --untardir <dir> --version <v>: unpack "a chart" and count the call.
        file_put_contents($this->tmp.'/bin/helm', "#!/bin/sh\nmkdir -p \"\$5/thechart\" && printf 'name: thechart\\nversion: %s\\n' \"\$7\" > \"\$5/thechart/Chart.yaml\"\necho pull >> \"".$this->tmp."/pulls\"\n");
        chmod($this->tmp.'/bin/helm', 0o755);
        $this->path = getenv('PATH');
        putenv('PATH='.$this->tmp.'/bin:'.$this->path);
    }

    protected function tearDown(): void
    {
        putenv('PATH='.$this->path);
        exec('rm -rf '.escapeshellarg($this->tmp));
    }

    public function testAPinnedChartIsPulledOnceAndKeyedByChartAndVersionAlone(): void
    {
        $provider = new ChartLocalCopyProvider($this->tmp.'/project', $this->tmp.'/kernel-cache', $this->tmp.'/charts');
        $ref = new ChartReference('oci://example.test/charts/thechart', '1.2.3');

        $first = $provider->createFrom($ref);
        $second = $provider->createFrom($ref);

        self::assertSame($first->path, $second->path);
        self::assertSame(ChartLocalCopyProvider::cachePath($this->tmp.'/charts', 'oci://example.test/charts/thechart', '1.2.3'), $first->path);
        self::assertStringStartsWith($this->tmp.'/charts/', $first->path, 'the shared cache, not the kernel cache');
        self::assertFileExists($first->path.'/Chart.yaml');
        self::assertSame("pull\n", file_get_contents($this->tmp.'/pulls'), 'one pull for two renders');
        self::assertSame([], glob($this->tmp.'/charts/.pull-*') ?: [], 'no pull directory left behind');
        self::assertSame(md5('oci://example.test/charts/thechart@1.2.3'), basename($first->path), 'no hour in a pinned chart\'s key');
    }

    public function testAChartAlreadyInPlaceIsNeverPulled(): void
    {
        $cached = ChartLocalCopyProvider::cachePath($this->tmp.'/charts', 'repo/thechart', '2.0.0');
        mkdir($cached, 0o755, true);
        file_put_contents($cached.'/Chart.yaml', "name: thechart\n");

        $provider = new ChartLocalCopyProvider($this->tmp.'/project', $this->tmp.'/kernel-cache', $this->tmp.'/charts');
        self::assertSame($cached, $provider->createFrom(new ChartReference('repo/thechart', '2.0.0'))->path);
        self::assertFileDoesNotExist($this->tmp.'/pulls');
    }

    public function testLatestIsKeyedByTheHour(): void
    {
        self::assertSame(md5('repo/thechart@latest_'.date('Y-m-d H')), basename(ChartLocalCopyProvider::cachePath('/c', 'repo/thechart', null)));
    }

    public function testWithoutAConfiguredDirectoryTheKernelCacheStandsInWhenThereIsNoHome(): void
    {
        $home = getenv('HOME');
        putenv('HOME='.$this->tmp.'/no-such-home');
        putenv('XDG_CACHE_HOME');
        putenv('KLASS8S_CHART_CACHE_DIR');
        try {
            $provider = new ChartLocalCopyProvider($this->tmp.'/project', $this->tmp.'/kernel-cache');
            $dir = $provider->createFrom(new ChartReference('repo/thechart', '3.0.0'));
            self::assertStringStartsWith($this->tmp.'/kernel-cache/helm/', $dir->path);
        } finally {
            putenv(false === $home ? 'HOME' : 'HOME='.$home);
        }
    }
}
