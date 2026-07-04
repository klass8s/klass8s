<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Event\ManifestsDumpedEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Service\Attribute\Required;

abstract class AbstractDumpCommand extends Command
{
    public const string OPTION_OUTPUT_DIR = 'output-dir';
    public const string OPTION_RECREATE_DIR = 'recreate-output-dir';

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        protected readonly string $manifestsDir,
        protected readonly string $currentEnv,
    ) {
        parent::__construct();
    }

    #[Required]
    public function setEventDispatcher(EventDispatcherInterface $dispatcher): void
    {
        $this->dispatcher = $dispatcher;
    }

    protected function notifyManifestsDumped(): void
    {
        $this->dispatcher->dispatch(new ManifestsDumpedEvent(), ManifestsDumpedEvent::NAME);
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

        $outputDir .= '/'.$this->currentEnv;

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
