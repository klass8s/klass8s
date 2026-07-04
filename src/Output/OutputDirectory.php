<?php

declare(strict_types=1);

namespace Klass8s\Output;

use Klass8s\Renderer\RendererInterface;
use Klass8s\Renderer\YamlRenderer;
use Klass8s\Value\YamlString;

final class OutputDirectory
{
    private const string YAML_DOCUMENT_SEPARATOR = "\n---\n";

    /**
     * Files are kept unrendered (RenderableFile holds the live resource object) until
     * render()/dumpTo(), so listeners on ManifestsProcessedEvent can still mutate
     * resources (e.g. checksum annotations) or add files before anything is serialized.
     *
     * @var array<string, RenderableFile|RenderedFile|OutputDirectory>
     */
    private array $files = [];

    public function __construct(public readonly string $name, public readonly RendererInterface $renderer)
    {
    }

    public function addDirectory(OutputDirectory $directory): void
    {
        if (array_key_exists($directory->name, $this->files)) {
            $type = $this->files[$directory->name] instanceof OutputDirectory ? 'Directory' : 'File';
            throw new \LogicException(sprintf('%s "%s" already exists in the output directory "%s" .', $type, $directory->name, $this->name));
        }

        $this->files[$directory->name] = $directory;
    }

    public function addFile(RenderableFile|RenderedFile $file): void
    {
        $filename = $file instanceof RenderableFile
            ? $file->nameWithoutExtension.$this->renderer->fileExtension()
            : $file->filename;

        if (array_key_exists($filename, $this->files)) {
            throw new \LogicException(sprintf('File "%s" already exists in the output directory "%s" .', $filename, $this->name));
        }

        $this->files[$filename] = $file;
    }

    public function dumpTo(string $directoryPath): void
    {
        $path = $directoryPath.'/'.$this->name;
        if (!is_dir($path)) {
            if (file_exists($path)) {
                throw new \LogicException(sprintf('Path for output directory "%s" already exists and it is a file.', $path));
            }

            mkdir($path, 0o777, true);
        }

        foreach ($this->files as $filename => $file) {
            if ($file instanceof OutputDirectory) {
                $file->dumpTo($path);
            } else {
                file_put_contents($path.'/'.$filename, $this->contentsOf($file));
            }
        }
    }

    public function render(): YamlString
    {
        if (!$this->renderer instanceof YamlRenderer) {
            throw new \LogicException(sprintf('"%s" supports rendering only in YAML format, but "%s" is given as a renderer.', __CLASS__, get_class($this->renderer)));
        }

        $bucket = new \ArrayObject();
        $this->renderTo($bucket, '');
        if (0 === $bucket->count()) {
            throw new \LogicException('No files were added to the output directory.');
        }

        return new YamlString(implode(self::YAML_DOCUMENT_SEPARATOR, $bucket->getArrayCopy()));
    }

    public function isEmpty(): bool
    {
        return empty($this->files);
    }

    private function renderTo(\ArrayObject $bucket, string $filenamePrefix): void
    {
        foreach ($this->files as $filename => $file) {
            $fullName = $filenamePrefix.'/'.$filename;
            if ($file instanceof OutputDirectory) {
                $file->renderTo($bucket, $fullName);
            } else {
                $headline = "# File: {$fullName}\n";
                $bucket[$fullName] = $headline.$this->contentsOf($file);
            }
        }
    }

    private function contentsOf(RenderableFile|RenderedFile $file): string
    {
        return $file instanceof RenderableFile ? $file->render($this->renderer) : $file->contents;
    }
}
