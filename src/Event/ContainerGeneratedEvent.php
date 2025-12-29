<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Core\V1\Container;
use Klass8s\Core\Container\ContainerInterface;

final readonly class ContainerGeneratedEvent
{
    public const string NAME = 'klass8s.container.generated';

    public function __construct(public Container $container, public ContainerInterface $builder)
    {
    }
}
