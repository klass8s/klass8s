<?php

declare(strict_types=1);

namespace Klass8s\Core\Container\Lifecycle\Action;

use K8Schema\Api\Core\V1\HTTPGetAction;
use K8Schema\Api\Core\V1\HTTPHeader;

class HttpGetActionBuilder
{
    private HTTPGetAction $action;

    public function __construct(HTTPGetAction $action)
    {
        $this->action = $action;
    }

    public function addHeader(string $name, string $value): self
    {
        $header = new HTTPHeader($name, $value);
        $this->action->httpHeaders()->add($header);

        return $this;
    }

    public function setHost(string $host): self
    {
        $this->action->setHost($host);

        return $this;
    }

    public function setPath(string $path): self
    {
        $this->action->setPath($path);

        return $this;
    }

    public function setPort(string|int $port): self
    {
        $this->action->setPort($port);

        return $this;
    }

    public function setScheme(string $scheme): self
    {
        $this->action->setScheme($scheme);

        return $this;
    }
}
