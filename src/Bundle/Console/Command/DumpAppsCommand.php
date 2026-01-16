<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use InvalidArgumentException;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Event\ManifestsDumpedEvent;
use Klass8s\Manifest\ManifestGenerator;
use Klass8s\Registry\AppRegistry;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsCommand(
    name: 'k8s:dump:apps',
    description: 'Processes apps and dumps Yaml manifests to the output dir',
)]
class DumpAppsCommand extends Command
{
    public const string ARGUMENT_APPS_ALIASES = 'apps-aliases';
    public const string OPTION_OUTPUT_DIR = 'output-dir';
    public const string OPTION_RECREATE_DIR = 'recreate-output-dir';

    public function __construct(
        private readonly ManifestGenerator $mg,
        private readonly AppRegistry $appRegistry,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly string $manifestsDir,
        private readonly string $env,
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addArgument(
                name: self::ARGUMENT_APPS_ALIASES,
                mode: InputArgument::IS_ARRAY,
                description: 'A space separated list of app aliases. If not specified, all apps will be processed.',
                default: [],
            )
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);
        $outputDirName = $this->getValidOutputDir($input, (bool)$input->getOption(self::OPTION_RECREATE_DIR));
        $aliases = $input->getArgument(self::ARGUMENT_APPS_ALIASES);
        if ([] === $aliases) {
            $aliases = $this->appRegistry->aliases();
        }

        $outputDir = $this->mg->generate(...$aliases);
        $outputDir->dumpTo($outputDirName);

        $this->dispatcher->dispatch(new ManifestsDumpedEvent(), ManifestsDumpedEvent::NAME);

        $io->success(sprintf('Manifests are dumped to <comment>%s</comment>.', $outputDirName));

        return self::SUCCESS;
    }

    private function getValidOutputDir(InputInterface $input, bool $recreateDir): string
    {
        $outputDir = $input->getOption(self::OPTION_OUTPUT_DIR) ?? $this->manifestsDir;
        if (null === $outputDir) {
            throw new InvalidArgumentException('Option "--output-dir" must be specified');
        }

        $outputDir .= '/'.$this->env;

        if (!file_exists($outputDir)) {
            try {
                mkdir($outputDir, 0o777, true);
            } catch (Throwable $error) {
                throw new RuntimeException(sprintf('Cannot create output dir "%s": %s', $outputDir, $error->getMessage()));
            }
        }
        $realpath = realpath($outputDir);
        if (!$realpath || !is_dir($realpath)) {
            throw new InvalidArgumentException(sprintf('Output path "%s" is not a directory', $outputDir));
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
