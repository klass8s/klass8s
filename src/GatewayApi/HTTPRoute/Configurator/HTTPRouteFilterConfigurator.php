<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\HTTPRoute\Configurator;

use Klass8s\GatewayApi\Enum\HTTPPathModifierType;

final class HTTPRouteFilterConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    /**
     * @param list<array{name: string, value: string}> $set
     * @param list<array{name: string, value: string}> $add
     * @param list<string>                              $remove
     */
    public function requestHeaderModifier(array $set = [], array $add = [], array $remove = []): static
    {
        $this->data['type'] = 'RequestHeaderModifier';
        $this->data['requestHeaderModifier'] = array_filter([
            'set' => $set,
            'add' => $add,
            'remove' => $remove,
        ], static fn (array $v): bool => [] !== $v);

        return $this;
    }

    /**
     * @param list<array{name: string, value: string}> $set
     * @param list<array{name: string, value: string}> $add
     * @param list<string>                              $remove
     */
    public function responseHeaderModifier(array $set = [], array $add = [], array $remove = []): static
    {
        $this->data['type'] = 'ResponseHeaderModifier';
        $this->data['responseHeaderModifier'] = array_filter([
            'set' => $set,
            'add' => $add,
            'remove' => $remove,
        ], static fn (array $v): bool => [] !== $v);

        return $this;
    }

    public function requestRedirect(
        ?string $scheme = null,
        ?string $hostname = null,
        ?int $port = null,
        ?int $statusCode = null,
        ?HTTPPathModifierType $pathType = null,
        ?string $pathValue = null,
    ): static {
        $this->data['type'] = 'RequestRedirect';
        $redirect = array_filter([
            'scheme' => $scheme,
            'hostname' => $hostname,
            'port' => $port,
            'statusCode' => $statusCode,
        ], static fn (mixed $v): bool => null !== $v);

        if (null !== $pathType && null !== $pathValue) {
            $redirect['path'] = [
                'type' => $pathType->value,
                $pathType === HTTPPathModifierType::ReplaceFullPath ? 'replaceFullPath' : 'replacePrefixMatch' => $pathValue,
            ];
        }

        $this->data['requestRedirect'] = $redirect;

        return $this;
    }

    public function urlRewrite(
        ?string $hostname = null,
        ?HTTPPathModifierType $pathType = null,
        ?string $pathValue = null,
    ): static {
        $this->data['type'] = 'URLRewrite';
        $rewrite = array_filter([
            'hostname' => $hostname,
        ], static fn (mixed $v): bool => null !== $v);

        if (null !== $pathType && null !== $pathValue) {
            $rewrite['path'] = [
                'type' => $pathType->value,
                $pathType === HTTPPathModifierType::ReplaceFullPath ? 'replaceFullPath' : 'replacePrefixMatch' => $pathValue,
            ];
        }

        $this->data['urlRewrite'] = $rewrite;

        return $this;
    }

    public function requestMirror(string $serviceName, int $port, ?int $percent = null, ?string $namespace = null): static
    {
        $this->data['type'] = 'RequestMirror';
        $mirror = [
            'backendRef' => array_filter([
                'name' => $serviceName,
                'port' => $port,
                'namespace' => $namespace,
            ], static fn (mixed $v): bool => null !== $v),
        ];

        if (null !== $percent) {
            $mirror['percent'] = $percent;
        }

        $this->data['requestMirror'] = $mirror;

        return $this;
    }

    public function extensionRef(string $group, string $kind, string $name): static
    {
        $this->data['type'] = 'ExtensionRef';
        $this->data['extensionRef'] = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        return $this;
    }
}
