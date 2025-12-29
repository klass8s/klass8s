<?php

declare(strict_types=1);

namespace Klass8s\Core\Secret;

use K8Schema\Api\Core\V1\Secret;
use K8Schema\Collection\StringMap;
use Klass8s\Core\AbstractManifest;

abstract class AbstractSecret extends AbstractManifest implements SecretInterface
{
    public function data(StringMap $data): void
    {
    }

    public function stringData(StringMap $stringData): void
    {
    }

    public function keysPrefix(): string
    {
        return '';
    }

    public function type(): SecretType
    {
        return SecretType::Opaque;
    }

    public function configureSecret(Secret $secret): void
    {
    }

    final public static function kind(): string
    {
        return Secret::KIND;
    }

    final public static function apiVersion(): string
    {
        return Secret::API_VERSION;
    }
}
