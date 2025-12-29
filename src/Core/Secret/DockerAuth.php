<?php

declare(strict_types=1);

namespace Klass8s\Core\Secret;

readonly class DockerAuth
{
    public function __construct(
        public string $host,
        public string $username,
        public string $password,
        public ?string $email = null,
    ) {
    }
}
