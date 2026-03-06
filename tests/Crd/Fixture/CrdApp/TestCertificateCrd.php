<?php

declare(strict_types=1);

namespace Klass8s\Tests\Crd\Fixture\CrdApp;

use Klass8s\Core\Crd\AbstractCustomResourceDefinition;
use Klass8s\Core\Crd\Configurator\CrdNamesConfigurator;
use Klass8s\Core\Crd\Configurator\CrdVersionsConfigurator;
use Klass8s\Core\Crd\CrdScope;
use Klass8s\Manifest\MetadataConfigurator;

class TestCertificateCrd extends AbstractCustomResourceDefinition
{
    public static function shortName(): string
    {
        return 'certificates';
    }

    public function group(): string
    {
        return 'cert-manager.io';
    }

    public function resourceKind(): string
    {
        return 'Certificate';
    }

    public function plural(): string
    {
        return 'certificates';
    }

    public function scope(): CrdScope
    {
        return CrdScope::Namespaced;
    }

    public function names(CrdNamesConfigurator $names): void
    {
        $names
            ->singular('certificate')
            ->listKind('CertificateList')
            ->shortNames('cert', 'certs')
            ->categories('cert-manager');
    }

    public function versions(CrdVersionsConfigurator $versions): void
    {
        $v1 = $versions->addVersion('v1');
        $v1->openApiV3Schema([
            'type' => 'object',
            'properties' => [
                'spec' => [
                    'type' => 'object',
                    'required' => ['secretName', 'issuerRef'],
                    'properties' => [
                        'secretName' => ['type' => 'string'],
                        'duration' => ['type' => 'string'],
                        'renewBefore' => ['type' => 'string'],
                        'dnsNames' => [
                            'type' => 'array',
                            'items' => ['type' => 'string'],
                        ],
                        'issuerRef' => [
                            'type' => 'object',
                            'required' => ['name', 'kind'],
                            'properties' => [
                                'name' => ['type' => 'string'],
                                'kind' => ['type' => 'string'],
                                'group' => ['type' => 'string'],
                            ],
                        ],
                    ],
                ],
                'status' => [
                    'type' => 'object',
                    'properties' => [
                        'ready' => ['type' => 'boolean'],
                        'expirationDate' => ['type' => 'string', 'format' => 'date-time'],
                    ],
                ],
            ],
        ]);

        $v1->addPrinterColumn('Ready', 'boolean', '.status.ready')
            ->description('Whether the certificate is ready');
        $v1->addPrinterColumn('Secret', 'string', '.spec.secretName');
        $v1->addPrinterColumn('Expiration', 'date', '.status.expirationDate')
            ->priority(1);

        $v1->subresources()->enableStatus();

        $v1->addSelectableField('.spec.issuerRef.name');

        $v1alpha1 = $versions->addVersion('v1alpha1', served: true, storage: false);
        $v1alpha1->deprecated(warning: 'cert-manager.io/v1alpha1 Certificate is deprecated; use v1');
        $v1alpha1->openApiV3Schema([
            'type' => 'object',
            'properties' => [
                'spec' => [
                    'type' => 'object',
                    'properties' => [
                        'secretName' => ['type' => 'string'],
                    ],
                ],
            ],
        ]);
    }

    public function metadata(MetadataConfigurator $meta): void
    {
        $meta->labels()->add('app.kubernetes.io/part-of', 'cert-manager');
    }
}
