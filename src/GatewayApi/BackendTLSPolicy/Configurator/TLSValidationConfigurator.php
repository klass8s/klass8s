<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

final class TLSValidationConfigurator
{
    public function __construct(private \ArrayObject $data)
    {
    }

    public function hostname(string $hostname): static
    {
        $this->data['hostname'] = $hostname;

        return $this;
    }

    public function caCertificateFromConfigMap(string $name, string $group = '', string $kind = 'ConfigMap'): static
    {
        if (!isset($this->data['caCertificateRefs'])) {
            $this->data['caCertificateRefs'] = [];
        }

        $this->data['caCertificateRefs'][] = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        return $this;
    }

    public function caCertificateFromSecret(string $name, string $group = '', string $kind = 'Secret'): static
    {
        if (!isset($this->data['caCertificateRefs'])) {
            $this->data['caCertificateRefs'] = [];
        }

        $this->data['caCertificateRefs'][] = [
            'group' => $group,
            'kind' => $kind,
            'name' => $name,
        ];

        return $this;
    }

    public function wellKnownCACertificates(string $type = 'System'): static
    {
        $this->data['wellKnownCACertificates'] = $type;

        return $this;
    }

    public function subjectAltNameHostname(string $hostname): static
    {
        if (!isset($this->data['subjectAltNames'])) {
            $this->data['subjectAltNames'] = [];
        }

        $this->data['subjectAltNames'][] = [
            'type' => 'Hostname',
            'hostname' => $hostname,
        ];

        return $this;
    }

    public function subjectAltNameURI(string $uri): static
    {
        if (!isset($this->data['subjectAltNames'])) {
            $this->data['subjectAltNames'] = [];
        }

        $this->data['subjectAltNames'][] = [
            'type' => 'URI',
            'uri' => $uri,
        ];

        return $this;
    }
}
