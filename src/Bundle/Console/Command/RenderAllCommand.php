<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\App\AppManager;
use Klass8s\Bridge\Helm\HelmReleaseManager;
use Klass8s\Bridge\Helm\Release\HelmReleaseRegistry;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Registry\AppRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'k8s:render:all',
    description: 'Renders manifests for all apps and Helm releases to stdout',
)]
class RenderAllCommand extends Command
{
    public function __construct(
        private readonly AppManager $appManager,
        private readonly AppRegistry $appRegistry,
        private readonly HelmReleaseManager $helmReleaseManager,
        private readonly HelmReleaseRegistry $helmReleaseRegistry,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);

        $appsDir = $this->appManager->generateManifests(...$this->appRegistry->aliases());
        $helmDir = $this->helmReleaseManager->generateManifests(...$this->helmReleaseRegistry->releaseNames);

        $io->writeln($appsDir->render()->toString());
        echo "\n---\n";
        $io->writeln($helmDir->render()->toString());

        return self::SUCCESS;
    }
}
