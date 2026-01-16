<?php

declare(strict_types=1);

namespace Klass8s\Config;

trait ConfigAwareTrait
{
    protected bool $configHasBeenSet = false;

    public protected(set) array $config = [] {
        get {
            if (!$this->configHasBeenSet) {
                throw new \RuntimeException('Config has not been set yet.');
            }

            return $this->config;
        }
        set {
            if ($this->configHasBeenSet) {
                throw new \RuntimeException('Config has already been set and cannot be modified.');
            }

            $this->config = $value;
            $this->configHasBeenSet = true;
        }
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
    }
}
