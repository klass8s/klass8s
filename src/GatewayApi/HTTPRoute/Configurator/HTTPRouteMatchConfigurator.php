<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

use Klass8s\GatewayApi\Enum\HeaderMatchType;
use Klass8s\GatewayApi\Enum\HTTPMethod;
use Klass8s\GatewayApi\Enum\PathMatchType;
use Klass8s\GatewayApi\Enum\QueryParamMatchType;

final class HTTPRouteMatchConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function path(PathMatchType $type, string $value): static
    {
        $this->data['path'] = [
            'type' => $type->value,
            'value' => $value,
        ];

        return $this;
    }

    public function pathPrefix(string $value): static
    {
        return $this->path(PathMatchType::PathPrefix, $value);
    }

    public function pathExact(string $value): static
    {
        return $this->path(PathMatchType::Exact, $value);
    }

    public function pathRegex(string $value): static
    {
        return $this->path(PathMatchType::RegularExpression, $value);
    }

    public function header(string $name, string $value, HeaderMatchType $type = HeaderMatchType::Exact): static
    {
        if (!isset($this->data['headers'])) {
            $this->data['headers'] = [];
        }

        $this->data['headers'][] = [
            'type' => $type->value,
            'name' => $name,
            'value' => $value,
        ];

        return $this;
    }

    public function queryParam(string $name, string $value, QueryParamMatchType $type = QueryParamMatchType::Exact): static
    {
        if (!isset($this->data['queryParams'])) {
            $this->data['queryParams'] = [];
        }

        $this->data['queryParams'][] = [
            'type' => $type->value,
            'name' => $name,
            'value' => $value,
        ];

        return $this;
    }

    public function method(HTTPMethod $method): static
    {
        $this->data['method'] = $method->value;

        return $this;
    }
}
