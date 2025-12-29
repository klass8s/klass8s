<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Env\EnvConfigurator;

readonly class SecretEnvSourceAddedEvent
{
    public const string NAME = 'klass8s.env_source.secret.added';

    public function __construct(
        public string $secretClass,
        public AppInterface $app,
        public EnvConfigurator $env,
    ) {
    }
}
