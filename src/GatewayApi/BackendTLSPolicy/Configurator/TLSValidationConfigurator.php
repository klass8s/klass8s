<?php

declare(strict_types=1);

namespace Klass8s\GatewayApi\BackendTLSPolicy\Configurator;

use Klass8s\App\AppInterface;
use Klass8s\Core\ConfigMap\ConfigMapInterface;
use Klass8s\Core\Secret\SecretInterface;

final class TLSValidationConfigurator
{
    public function __construct(
        private \ArrayObject $data,
        private AppInterface $app,
    ) {
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

    /**
     * @param class-string<ConfigMapInterface> $configMapClass
     */
    public function caCertificateFromConfigMapClass(string $configMapClass): static
    {
        $name = $this->app->resourceNameFromManifestClass($configMapClass, ConfigMapInterface::class);

        return $this->caCertificateFromConfigMap($name);
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

    /**
     * @param class-string<SecretInterface> $secretClass
     */
    public function caCertificateFromSecretClass(string $secretClass): static
    {
        $name = $this->app->resourceNameFromManifestClass($secretClass, SecretInterface::class);

        return $this->caCertificateFromSecret($name);
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
