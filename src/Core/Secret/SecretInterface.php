<?php

declare(strict_types=1);

namespace Klass8s\Core\Secret;

use K8Schema\Api\Core\V1\Secret;
use K8Schema\Collection\StringMap;
use Klass8s\Manifest\ManifestInterface;

interface SecretInterface extends ManifestInterface
{
    public function data(StringMap $data): void;
    public function stringData(StringMap $stringData): void;
    public function keysPrefix(): string;
    public function type(): SecretType;
    public function configureSecret(Secret $secret): void;
}
