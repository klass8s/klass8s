<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\ManifestGenerator\ManifestsGenerationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:print:all',
    description: 'Prints all manifests to stdout',
)]
class PrintAllCommand extends Command
{
    public function __construct(private readonly ManifestsGenerationService $generationService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $this->generationService->processAll();
        $rendered = $this->generationService->renderAll();
        $delimiter = PHP_EOL.'---'.PHP_EOL;
        $output = implode($delimiter, $rendered);

        $io->writeln($output.$delimiter);

        return self::SUCCESS;
    }
}
