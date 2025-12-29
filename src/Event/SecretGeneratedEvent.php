<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Core\V1\Secret;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\Secret\SecretInterface;

final readonly class SecretGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.secret';

    public function __construct(
        private SecretInterface $manifest,
        private Secret $secret,
        private AppInterface $app,
    ) {
    }

    public function manifest(): SecretInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->secret;
    }

    public function secret(): Secret
    {
        return $this->secret;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
