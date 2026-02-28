<?php

declare(strict_types=1);

namespace Klass8s\Bridge\Helm\Release;

use Klass8s\Bridge\Helm\Chart\Reference\ChartLocalCopyProvider;
use Klass8s\Event\Helm\HelmManifestProcessedEvent;
use Klass8s\Output\OutputDirectory;
use Klass8s\Output\RenderableFile;
use Klass8s\Renderer\RendererInterface;
use Klass8s\Util\StringUtil;
use Klass8s\Value\YamlString;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

final readonly class HelmReleaseProcessor
{
    public function __construct(
        private ChartLocalCopyProvider $localCopyProvider,
        private RendererInterface $renderer,
        private EventDispatcherInterface $dispatcher,
        private string $projectDir,
    ) {
    }

    public function process(HelmReleaseInterface $release): OutputDirectory
    {
        $yaml = $this->render($release);
        $manifests = explode("\n---\n", $yaml->value) |> array_filter(...);
        $manifests = array_filter(
            $manifests,
            static function (string $manifest): bool {
                $lines = explode(PHP_EOL, $manifest);
                $lines = array_filter(
                    $lines,
                    static fn (string $line) => !empty(trim($line)) && !str_starts_with($line, '#') && !str_starts_with($line, '---')
                );
                $manifest = implode(PHP_EOL, $lines) |> trim(...);

                return !empty($manifest);
            }
        );

        $outputDir = new OutputDirectory($release->name, $this->renderer);
        foreach ($manifests as $manifest) {
            $manifest = Yaml::parse($manifest, Yaml::PARSE_OBJECT_FOR_MAP);
            if (!is_object($manifest)) {
                throw new \RuntimeException(sprintf('Failed to parse manifest "%s".', $manifest));
            }
            $name = $manifest->metadata->name;

            $event = new HelmManifestProcessedEvent($manifest->kind, $name, $manifest, $release);
            $this->dispatcher->dispatch($event, HelmManifestProcessedEvent::NAME);

            $fileName = sprintf('%s.%s', $name, StringUtil::asDNSSubdomain($manifest->kind));
            $file = new RenderableFile($fileName, $manifest);
            $outputDir->addFile($file);
        }

        return $outputDir;
    }

    public function render(HelmReleaseInterface $release): YamlString
    {
        $chartDir = $this->localCopyProvider->createFrom($release->chart);
        $command = [
            'helm',
            'template',
            $release->name,
            $chartDir->path,
            '--include-crds',
        ];

        $values = $release->values();
        if (!empty($values)) {
            $valuesYaml = Yaml::dump($values);
            $valuesFilename = self::valuesYamlTempFilename($release);
            file_put_contents($valuesFilename, $valuesYaml);

            $command[] = '--values';
            $command[] = $valuesFilename;
        }

        $process = new Process($command, $this->projectDir);
        $process->mustRun();

        return new YamlString($process->getOutput());
    }

    private static function valuesYamlTempFilename(HelmReleaseInterface $release): string
    {
        $fileName = tempnam(sys_get_temp_dir(), sprintf('helm-%s-values', $release->name));
        if (false === $fileName) {
            throw new \RuntimeException('Failed to create temporary file for Helm release values.');
        }
        $filenameWithExtension = $fileName.'.yaml';
        while (file_exists($filenameWithExtension)) {
            $filenameWithExtension = $fileName.'-'.uniqid().'.yaml';
        }

        return $filenameWithExtension;
    }
}
