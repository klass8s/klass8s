<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus\Configurator;

final class NamespaceSelectorConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function any(bool $any): static
    {
        $this->data['any'] = $any;

        return $this;
    }

    /**
     * @param string[] $names
     */
    public function matchNames(array $names): void
    {
        $this->data['matchNames'] = $names;
    }
}
