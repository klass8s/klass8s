<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Env\EnvConfigurator;

readonly class ConfigMapEnvSourceAddedEvent
{
    public const string NAME = 'klass8s.env_source.config_map.added';

    public function __construct(
        public string $configMapClass,
        public AppInterface $app,
        public EnvConfigurator $env
    ) {
    }
}
