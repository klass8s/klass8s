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
     * @var array<string, RenderedFile|OutputDirectory>
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
        if ($file instanceof RenderableFile) {
            $filename = $file->nameWithoutExtension.'.'.$this->renderer->fileExtension();
            $contents = $this->renderer->render($file->resource);
            $file = new RenderedFile($filename, $contents);
        }

        if (array_key_exists($file->filename, $this->files)) {
            throw new \LogicException(sprintf('File "%s" already exists in the output directory "%s" .', $file->filename, $this->name));
        }

        $this->files[$file->filename] = $file;
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

        foreach ($this->files as $file) {
            if ($file instanceof OutputDirectory) {
                $file->dumpTo($path);
            } else {
                file_put_contents($path.'/'.$file->filename, $file->contents);
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

        return new YamlString(implode(self::YAML_DOCUMENT_SEPARATOR, $bucket->getArrayCopy()));
    }

    private function renderTo(\ArrayObject $bucket, string $filenamePrefix): void
    {
        foreach ($this->files as $filename => $file) {
            $fullName = $filenamePrefix.'/'.$filename;
            if ($file instanceof OutputDirectory) {
                $file->renderTo($bucket, $fullName);
            } else {
                $headline = "# File: {$fullName}\n";
                $bucket[$fullName] = $headline.$file->contents;
            }
        }
    }
}
