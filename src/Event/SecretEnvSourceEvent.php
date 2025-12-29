<?php

declare(strict_types=1);

namespace Klass8s\Event;

use Klass8s\App\AppInterface;
use Klass8s\Core\Container\Env\EnvConfigurator;
use Psr\EventDispatcher\StoppableEventInterface;

class SecretEnvSourceEvent implements StoppableEventInterface
{
    public const string NAME = 'klass8s.env_source.secret';

    private bool $propagationStopped = false;

    public function __construct(
        public readonly string $secretClass,
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
