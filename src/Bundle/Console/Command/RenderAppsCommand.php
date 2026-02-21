<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\App\AppManager;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Registry\AppRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:render:apps',
    description: 'Renders manifests for specified apps to stdout',
)]
class RenderAppsCommand extends Command
{
    public const string ARGUMENT_APPS_ALIASES = 'apps-aliases';

    public function __construct(
        private readonly AppManager $appManager,
        private readonly AppRegistry $appRegistry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_APPS_ALIASES,
                InputArgument::IS_ARRAY,
                'A space separated list of app aliases. If not specified, all apps will be rendered.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $aliases = $input->getArgument(self::ARGUMENT_APPS_ALIASES);
        if ([] === $aliases) {
            $aliases = $this->appRegistry->aliases();
        }

        $outputDir = $this->appManager->generateManifests(...$aliases);
        if (!$outputDir->isEmpty()) {
            $io->writeln($outputDir->render()->toString());
        }

        return self::SUCCESS;
    }
}
