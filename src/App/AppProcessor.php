<?php

declare(strict_types=1);

namespace Klass8s\App;

use Klass8s\Core\ManifestProcessor;
use Klass8s\Registry\AppRegistry;
use Klass8s\Registry\ManifestRegistry;
use Klass8s\Registry\Query\ManifestsQuery;

readonly class AppProcessor
{
    public function __construct(
        private AppRegistry $appRegistry,
        private ManifestRegistry $manifestRegistry,
        private ManifestProcessor $manifestProcessor,
    ) {
    }

    public function process(string $appAlias): void
    {
        $app = $this->appRegistry->get($appAlias);
        $query = $this->createQuery($appAlias);
        foreach ($query->execute() as $manifest) {
            $this->manifestProcessor->process($manifest, $app);
        }
    }

    public function processInstancesOf(string $appAlias, array $classNames): void
    {
        $app = $this->appRegistry->get($appAlias);
        $processedManifests = [];
        foreach ($classNames as $className) {
            $query = $this->createQuery($appAlias);
            $query->instancesOf($className);

            foreach ($query->execute() as $manifest) {
                if (in_array($manifest, $processedManifests)) {
                    continue;
                }
                $this->manifestProcessor->process($manifest, $app);
            }
        }
    }

    private function createQuery(string $appAlias): ManifestsQuery
    {
        return $this->manifestRegistry->query($appAlias);
    }
}
