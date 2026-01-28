<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bridge\Helm\HelmReleaseManager;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:dump:helm',
    description: 'Processes Helm releases and dumps Yaml manifests to the output dir',
)]
class DumpHelmCommand extends AbstractDumpCommand
{
    use AddHelmReposTrait;

    public const string ARGUMENT_HELM_RELEASES = 'helm-releases';

    public function __construct(
        string $manifestsDir,
        string $currentEnv,
        private readonly HelmReleaseManager $releaseManager,
        private readonly HelmReleaseRegistry $registry,
    ) {
        parent::__construct($manifestsDir, $currentEnv);
    }

    #[\Override]
    public function configure(): void
    {
        $this
            ->addArgument(
                name: self::ARGUMENT_HELM_RELEASES,
                mode: InputArgument::IS_ARRAY,
                description: 'A space separated list of Helm release names. If not specified, all releases will be dumped.',
                default: [],
            )
        ;

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);
        $outputDirPath = $this->getValidOutputDir($input, (bool)$input->getOption(self::OPTION_RECREATE_DIR));
        $releaseNames = $input->getArgument(self::ARGUMENT_HELM_RELEASES);
        if ([] === $releaseNames) {
            $releaseNames = $this->registry->releaseNames;
        }

        $this->addHelmRepos();
        $outputDir = $this->releaseManager->generateManifests(...$releaseNames);
        $outputDir->dumpTo($outputDirPath);

        if ($outputDir->isEmpty()) {
            $io->warning('No manifests were generated.');
        } else {
            $io->success(sprintf('Manifests are dumped to "<comment>%s</comment>".', $outputDirPath));
        }

        return self::SUCCESS;
    }
}
