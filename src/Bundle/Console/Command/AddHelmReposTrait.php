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
        if ([] === $this->helmRepos) {
            return;
        }

        $existing = $this->helmCli->repoList();

        foreach ($this->helmRepos as $name => $url) {
            if (isset($existing[$name]) && $existing[$name] === rtrim($url, '/')) {
                continue;
            }

            $this->helmCli->repoAdd($name, $url);
        }
    }
}
