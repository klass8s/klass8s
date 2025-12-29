<?php

declare(strict_types=1);

namespace Klass8s\Core\Pod;

use K8Schema\Api\Core\V1\ObjectFieldSelector;

class PodField
{
    private const string METADATA_NAME = 'metadata.name';
    private const string METADATA_NAMESPACE = 'metadata.namespace';
    private const string METADATA_LABELS = 'metadata.labels';
    private const string METADATA_ANNOTATIONS = 'metadata.annotations';
    private const string METADATA_UID = 'metadata.uid';
    private const string NODE_NAME = 'spec.nodeName';
    private const string SERVICE_ACCOUNT_NAME = 'spec.serviceAccountName';
    private const string HOST_IP = 'status.hostIP';
    private const string POD_IP = 'status.podIP';

    private ObjectFieldSelector $selector;

    private function __construct(string $fieldPath)
    {
        $this->selector = new ObjectFieldSelector($fieldPath);
    }

    public function setApiVersion(string $apiVersion): void
    {
        $this->selector->setApiVersion($apiVersion);
    }

    public function selector(): ObjectFieldSelector
    {
        return $this->selector;
    }

    public static function metadataName(): self
    {
        return new self(self::METADATA_NAME);
    }

    public static function metadataNamespace(): self
    {
        return new self(self::METADATA_NAMESPACE);
    }

    public static function metadataLabels(): self
    {
        return new self(self::METADATA_LABELS);
    }

    public static function metadataAnnotations(): self
    {
        return new self(self::METADATA_ANNOTATIONS);
    }

    public static function metadataUid(): self
    {
        return new self(self::METADATA_UID);
    }

    public static function nodeName(): self
    {
        return new self(self::NODE_NAME);
    }

    public static function serviceAccountName(): self
    {
        return new self(self::SERVICE_ACCOUNT_NAME);
    }

    public static function hostIp(): self
    {
        return new self(self::HOST_IP);
    }

    public static function podIp(): self
    {
        return new self(self::POD_IP);
    }
}
