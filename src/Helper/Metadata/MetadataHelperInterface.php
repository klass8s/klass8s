<?php

declare(strict_types=1);

namespace Klass8s\Helper\Metadata;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Core\MetadataAwareInterface;
use Klass8s\Helper\HelperInterface;

interface MetadataHelperInterface extends HelperInterface
{
    public function configureMeta(MetadataAwareInterface $metadataAware, APIResourceInterface $apiObject): ObjectMeta;
}
