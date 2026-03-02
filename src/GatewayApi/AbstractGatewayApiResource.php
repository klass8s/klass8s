<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;

abstract class AbstractGatewayApiResource implements APIResourceInterface
{
    public const string API_VERSION = 'gateway.networking.k8s.io/v1';

    public function __construct(protected \ArrayObject $data)
    {
    }

    public function metadata(): ObjectMeta
    {
        return $this->data['metadata'];
    }

    public function jsonSerialize(): \ArrayObject
    {
        return $this->data;
    }
}
