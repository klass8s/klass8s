<?php

declare(strict_types=1);

namespace Klass8s\Monitoring\Prometheus;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;

abstract class AbstractPrometheusApiResource implements APIResourceInterface
{
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
