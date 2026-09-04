<?php

declare(strict_types=1);

namespace Klass8s\Tests\Storage\Fixture\StorageApp;

use K8Schema\Collection\StringMap;
use Klass8s\Core\Persistence\AbstractStorageClass;
use Klass8s\Core\Persistence\AllowedTopologiesConfigurator;
use Klass8s\Core\Persistence\ReclaimPolicy;

class TestStorageClass extends AbstractStorageClass
{
    public static function shortName(): string
    {
        return 'fast';
    }

    public function provisioner(): string
    {
        return 'example.io/disk';
    }

    public function parameters(StringMap $parameters): void
    {
        $parameters
            ->add('tier', 'premium')
            ->add('fsType', 'xfs');
    }

    public function reclaimPolicy(): ReclaimPolicy
    {
        return ReclaimPolicy::Retain;
    }

    public function mountOptions(): iterable
    {
        yield 'noatime';
        yield 'nodiratime';
    }

    public function allowedTopologies(AllowedTopologiesConfigurator $topologies): void
    {
        $topologies->addTerm()
            ->matchLabel('topology.kubernetes.io/zone', 'eu-central-1a', 'eu-central-1b')
            ->matchLabel('example.io/disk-tier', 'premium');
        $topologies->addTerm()
            ->matchLabel('topology.kubernetes.io/zone', 'eu-central-1c');
    }
}
