<?php

declare(strict_types=1);

namespace Klass8s\Event;

use K8Schema\Api\Networking\V1\Ingress;
use K8Schema\APIResourceInterface;
use Klass8s\App\AppInterface;
use Klass8s\Core\Ingress\IngressInterface;

final readonly class IngressGeneratedEvent implements ManifestGeneratedEventInterface
{
    public const string NAME = 'klass8s.manifest_generated.ingress';

    public function __construct(
        private IngressInterface $manifest,
        private Ingress $ingress,
        private AppInterface $app,
    ) {
    }

    public function manifest(): IngressInterface
    {
        return $this->manifest;
    }

    public function apiResource(): APIResourceInterface
    {
        return $this->ingress;
    }

    public function ingress(): Ingress
    {
        return $this->ingress;
    }

    public function app(): AppInterface
    {
        return $this->app;
    }
}
