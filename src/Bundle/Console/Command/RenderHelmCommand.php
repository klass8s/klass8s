<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bridge\Helm\HelmReleaseManager;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:render:helm',
    description: 'Renders manifests for specified Helm releases to stdout',
)]
class RenderHelmCommand extends Command
{
    use AddHelmReposTrait;

    private const string ARGUMENT_HELM_RELEASES = 'helm-releases';

    public function __construct(
        private readonly HelmReleaseManager $releaseManager,
        private readonly HelmReleaseRegistry $registry,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_HELM_RELEASES,
                InputArgument::IS_ARRAY,
                'A space separated list of Helm release names. If not specified, all releases will be rendered.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $releaseNames = $input->getArgument(self::ARGUMENT_HELM_RELEASES);
        if ([] === $releaseNames) {
            $releaseNames = $this->registry->releaseNames;
        }

        $this->addHelmRepos();
        $outputDir = $this->releaseManager->generateManifests(...$releaseNames);

        if (!$outputDir->isEmpty()) {
            $io->writeln($outputDir->render()->toString());
        }

        return self::SUCCESS;
    }
}
