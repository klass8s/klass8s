<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Env\EnvConfigurator;
use Psr\EventDispatcher\StoppableEventInterface;

class ConfigMapEnvSourceEvent implements StoppableEventInterface
{
    public const string NAME = 'klass8s.env_source.config_map';

    private bool $propagationStopped = false;

    public function __construct(
        public readonly string $configMapClass,
        public readonly AppInterface $app,
        public readonly EnvConfigurator $env,
    ) {
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }
}
