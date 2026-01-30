<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bundle\CodeGeneration\AppGenerator;
use Klass8s\Bundle\Console\UI\ModernStyle;
use Klass8s\Util\StringUtil;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Symfony\Component\String\u;

#[AsCommand(
    name: 'k8s:make:app',
    description: 'Creates a new K8S app skeleton',
)]
class MakeAppCommand extends Command
{
    private const string ARGUMENT_APP_NAME = 'app-name';

    public function __construct(private AppGenerator $generator)
    {
        parent::__construct();
    }

    public function configure(): void
    {
        $this
            ->addArgument(
                self::ARGUMENT_APP_NAME,
                InputArgument::REQUIRED,
                'App name without "app" suffix (e.g. "<fg=yellow>my</>" will result in MyApp class)'
            )
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new ModernStyle($input, $output);
        $appName = $input->getArgument(self::ARGUMENT_APP_NAME);
        try {
            $fileName = $this->generator->generate(StringUtil::asDNSSubdomain($appName));
        } catch (\Throwable $e) {
            $io->error($e->getMessage());
            $io->newLine();

            return self::FAILURE;
        }

        $io->success(sprintf('App "%s" successfully generated, see file "%s"', $appName, $fileName));

        return self::SUCCESS;
    }
}
