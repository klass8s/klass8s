<?php

declare(strict_types=1);

namespace Klass8s\Util;

use Klass8s\Core\DynamicNameAwareInterface;
use Klass8s\Core\FullNameAwareInterface;
use Klass8s\Core\ManifestInterface;

final readonly class ManifestShortName
{
    public static function getFrom(ManifestInterface $manifest): string
    {
        if ($manifest instanceof FullNameAwareInterface) {
            throw new \LogicException(sprintf('Instances of class "%s" are full name aware (see "%s"), therefore manifest short name cannot be derived', ClassName::real($manifest), FullNameAwareInterface::class));
        }

        return $manifest instanceof DynamicNameAwareInterface
            ? $manifest->name()
            : $manifest::shortName();
    }
}
