<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\App\AppManager;
use Klass8s\Bridge\Helm\HelmReleaseManager;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Registry\AppRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:dump:all',
    description: 'Dumps all apps and Helm releases to the output dir',
)]
class DumpAllCommand extends AbstractDumpCommand
{
    use AddHelmReposTrait;

    public function __construct(
        string $manifestsDir,
        string $env,
        private readonly AppManager $appManager,
        private readonly AppRegistry $appRegistry,
        private readonly HelmReleaseManager $helmReleaseManager,
        private readonly HelmReleaseRegistry $helmReleaseRegistry,
    ) {
        parent::__construct($manifestsDir, $env);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $outputDirPath = $this->getValidOutputDir($input, (bool)$input->getOption(self::OPTION_RECREATE_DIR));

        $appsDir = $this->appManager->generateManifests(...$this->appRegistry->aliases());

        $releaseNames = $this->helmReleaseRegistry->releaseNames;
        $this->addHelmRepos();
        $helmDir = $this->helmReleaseManager->generateManifests(...$releaseNames);

        $appsDir->dumpTo($outputDirPath);
        $helmDir->dumpTo($outputDirPath);

        if ($appsDir->isEmpty() && $helmDir->isEmpty()) {
            $io->warning('No manifests were generated.');
        } else {
            $io->success(sprintf('Manifests are dumped to "<comment>%s</comment>".', $outputDirPath));
        }

        return self::SUCCESS;
    }
}
