<?php

declare(strict_types=1);

namespace Klass8s\Helper\Metadata;

use K8Schema\Apimachinery\Pkg\Apis\Meta\V1\ObjectMeta;
use K8Schema\APIResourceInterface;
use Klass8s\Core\FullNameAwareInterface;
use Klass8s\Core\ManifestInterface;
use Klass8s\Core\MetadataAwareInterface;
use Klass8s\Core\MetadataConfigurator;
use Klass8s\Helper\Names\NamesHelperInterface;
use Klass8s\Util\ManifestShortName;

class MetadataHelper implements MetadataHelperInterface
{
    private NamesHelperInterface $namesHelper;

    public function configureMeta(MetadataAwareInterface $metadataAware, APIResourceInterface $apiObject): ObjectMeta
    {
        $meta = $apiObject->metadata();
        $metaConfigurator = new MetadataConfigurator($meta->labels(), $meta->annotations());
        $metadataAware->metadata($metaConfigurator);

        if ($metadataAware instanceof ManifestInterface) {
            $name = $metadataAware instanceof FullNameAwareInterface
                ? $metadataAware->fullName()
                : $this->namesHelper->fullName(ManifestShortName::getFrom($metadataAware));
            $meta->setName($name);
        }

        return $meta;
    }

    public function setNamesHelper(NamesHelperInterface $namesHelper): void
    {
        $this->namesHelper = $namesHelper;
    }
}
