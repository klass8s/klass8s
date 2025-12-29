<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EnvManagement\Attribute;

abstract readonly class AbstractMethodDecoratorAttribute
{
    public function __construct(protected string $methodName, protected array $forEnvs, protected bool $replacesReturnValue = false)
    {
    }

    public function methodName(): string
    {
        return $this->methodName;
    }

    /**
     * @return string[]
     */
    public function envs(): array
    {
        return $this->forEnvs;
    }

    public function replacesReturnValue(): bool
    {
        return $this->replacesReturnValue;
    }
}
