<?php

declare(strict_types=1);

namespace Klass8s\Core\Job;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Container\ContainerTrait;

abstract class AbstractContainerJob extends AbstractJob implements ContainerInterface
{
    use ContainerTrait;

    public function containers(): iterable
    {
        yield $this;
    }
}
