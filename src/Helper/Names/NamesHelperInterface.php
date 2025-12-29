<?php

declare(strict_types=1);

namespace Klass8s\Helper\Names;

use Klass8s\Helper\HelperInterface;

interface NamesHelperInterface extends HelperInterface
{
    public function fullName(string $shortName): string;
    public function byManifestClass(string $manifestClass): string;
    public function byConfigMapClass(string $configMapClass): string;
    public function bySecretClass(string $configMapClass): string;
    public function byServiceClass(string $serviceClass): string;
}
