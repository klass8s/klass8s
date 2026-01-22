<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractDumpCommand extends Command
{
    public const string OPTION_OUTPUT_DIR = 'output-dir';
    public const string OPTION_RECREATE_DIR = 'recreate-output-dir';

    public function __construct(
        protected readonly string $manifestsDir,
        protected readonly string $env,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addOption(
                name: self::OPTION_OUTPUT_DIR,
                shortcut: 'D',
                mode: InputOption::VALUE_REQUIRED,
                description: 'A directory where to save the Yaml manifests.',
            )
            ->addOption(
                name: self::OPTION_RECREATE_DIR,
                shortcut: 'R',
                mode: InputOption::VALUE_NONE,
                description: 'If specified, the output directory will be deleted and recreated.',
            )
        ;
    }

    protected function getValidOutputDir(InputInterface $input, bool $recreateDir): string
    {
        $outputDir = $input->getOption(self::OPTION_OUTPUT_DIR) ?? $this->manifestsDir;
        if (null === $outputDir) {
            throw new \InvalidArgumentException('Option "--output-dir" must be specified.');
        }

        $outputDir .= '/'.$this->env;

        if (!file_exists($outputDir)) {
            try {
                mkdir($outputDir, 0o777, true);
            } catch (\Throwable $error) {
                throw new \RuntimeException(sprintf('Cannot create output dir "%s": %s', $outputDir, $error->getMessage()));
            }
        }
        $realpath = realpath($outputDir);
        if (!$realpath || !is_dir($realpath)) {
            throw new \InvalidArgumentException(sprintf('Output path "%s" is not a directory.', $outputDir));
        }
        $outputDir = $realpath;

        if ($recreateDir) {
            $fs = new Filesystem();
            $fs->remove($outputDir);
            $fs->mkdir($outputDir);
        }

        return $realpath;
    }
}
