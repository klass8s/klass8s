<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class EnabledForEnvs
{
    /**
     * @var string[]
     */
    private array $envs;

    public function __construct(string ...$envs)
    {
        $this->envs = $envs;
    }

    /**
     * @return string[]
     */
    public function envs(): array
    {
        return $this->envs;
    }
}
