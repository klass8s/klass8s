<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Image;

enum ImagePullPolicy: string
{
    case Always = 'Always';
    case Never = 'Never';
    case IfNotPresent = 'IfNotPresent';

    public function toString(): string
    {
        return $this->value;
    }
}
