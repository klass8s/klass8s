<?php

declare(strict_types=1);

namespace Klass8s\Core\Secret;

use K8Schema\Collection\StringMap;
use Klass8s\Core\MetadataConfigurator;

abstract class AbstractServiceAccountSecret extends AbstractSecret
{
    private const string ANNOTATION_KEY = 'kubernetes.io/service-account.name';

    abstract protected function serviceAccountName(): string;

    final public function data(StringMap $data): void
    {
        $this->extraData($data);
    }

    final public function stringData(StringMap $stringData): void
    {
    }

    public function metadata(MetadataConfigurator $meta): void
    {
        $meta->annotations()->add(self::ANNOTATION_KEY, $this->serviceAccountName());
    }

    final public function type(): SecretType
    {
        return SecretType::ServiceAccountToken;
    }

    protected function extraData(StringMap $data): void
    {
    }
}
