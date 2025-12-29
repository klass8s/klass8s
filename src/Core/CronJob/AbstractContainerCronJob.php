<?php

declare(strict_types=1);

namespace Klass8s\Core\CronJob;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Container\ContainerTrait;

abstract class AbstractContainerCronJob extends AbstractCronJob implements ContainerInterface
{
    use ContainerTrait;

    public function containers(): iterable
    {
        yield $this;
    }
}
