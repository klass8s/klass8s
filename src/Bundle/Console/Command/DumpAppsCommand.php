<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\App\AppManager;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Event\ManifestsDumpedEvent;
use Klass8s\Registry\AppRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'k8s:dump:apps',
    description: 'Processes apps and dumps Yaml manifests to the output dir',
)]
class DumpAppsCommand extends AbstractDumpCommand
{
    public const string ARGUMENT_APPS_ALIASES = 'apps-aliases';

    public function __construct(
        string $manifestsDir,
        string $env,
        private readonly AppManager $appManager,
        private readonly AppRegistry $registry,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
        parent::__construct($manifestsDir, $env);
    }

    #[\Override]
    public function configure(): void
    {
        $this
            ->addArgument(
                name: self::ARGUMENT_APPS_ALIASES,
                mode: InputArgument::IS_ARRAY,
                description: 'A space separated list of app aliases. If not specified, all apps will be dumped.',
                default: [],
            )
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);
        $outputDirName = $this->getValidOutputDir($input, (bool)$input->getOption(self::OPTION_RECREATE_DIR));
        $aliases = $input->getArgument(self::ARGUMENT_APPS_ALIASES);
        if ([] === $aliases) {
            $aliases = $this->registry->aliases();
        }

        $outputDir = $this->appManager->generateManifests(...$aliases);
        $outputDir->dumpTo($outputDirName);

        $this->dispatcher->dispatch(new ManifestsDumpedEvent(), ManifestsDumpedEvent::NAME);

        $io->success(sprintf('Manifests are dumped to <comment>%s</comment>.', $outputDirName));

        return self::SUCCESS;
    }
}
