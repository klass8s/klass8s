<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\Manifest\ScaledObject\Trigger\RabbitMQ;

enum Mode: string
{
    case QueueLength = 'QueueLength';
    case MessageRate = 'MessageRate';
}
