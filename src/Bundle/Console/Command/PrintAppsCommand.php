<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\ManifestGenerator\ManifestsGenerationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:print:apps',
    description: 'Prints manifests for specified apps to stdout',
)]
class PrintAppsCommand extends Command
{
    public const string ARGUMENT_APPS_ALIASES = 'apps-aliases';

    public function __construct(private readonly ManifestsGenerationService $generationService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_APPS_ALIASES,
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Aliases (names) of apps, separated by space'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $aliases = $input->getArgument(self::ARGUMENT_APPS_ALIASES);

        $this->generationService->processApps(...$aliases);
        $rendered = $this->generationService->renderApps(...$aliases);
        $delimiter = PHP_EOL.'---'.PHP_EOL;
        $output = implode($delimiter, $rendered);

        $io->writeln($output.$delimiter);

        return self::SUCCESS;
    }
}
