<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Console\Command;

use Klass8s\Bridge\Helm\Cli\HelmCli;
use Symfony\Contracts\Service\Attribute\Required;

trait AddHelmReposTrait
{
    private HelmCli $helmCli;
    private array $helmRepos = [];

    #[Required]
    public function setHelmCli(HelmCli $helmCli): void
    {
        $this->helmCli = $helmCli;
    }

    #[Required]
    public function setHelmRepos(array $helmRepos): void
    {
        $this->helmRepos = $helmRepos;
    }

    protected function addHelmRepos(): void
    {
        foreach ($this->helmRepos as $name => $url) {
            $this->helmCli->repoAdd($name, $url);
        }
    }
}
