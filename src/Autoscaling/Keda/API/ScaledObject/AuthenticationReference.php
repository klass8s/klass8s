<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling\Keda\API\ScaledObject;

final readonly class AuthenticationReference implements \JsonSerializable
{
    public function __construct(public string $name)
    {
    }

    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
