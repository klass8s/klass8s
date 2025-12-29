<?php

declare(strict_types=1);

namespace Klass8s\Core\StatefulSet;

use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Core\Container\ContainerTrait;

abstract class AbstractContainerStatefulSet extends AbstractStatefulSet implements ContainerInterface
{
    use ContainerTrait;

    public function containers(): iterable
    {
        yield $this;
    }
}
