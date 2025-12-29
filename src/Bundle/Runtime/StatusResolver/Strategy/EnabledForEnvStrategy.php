<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Runtime\StatusResolver\Strategy;

use Klass8s\Bundle\EnvManagement\Attribute\EnabledForEnvs;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Runtime\ManifestStatusResolverStrategyInterface;

final readonly class EnabledForEnvStrategy implements ManifestStatusResolverStrategyInterface
{
    public function __construct(private string $env)
    {
    }

    public function isClassEnabled(\ReflectionClass $class): bool
    {
        $attributes = AttributesUtil::getClassAttributes($class, EnabledForEnvs::class);
        while ($parentClass = $class->getParentClass()) {
            $parentAttributes = AttributesUtil::getClassAttributes($parentClass, EnabledForEnvs::class);
            $attributes = [...$attributes, ...$parentAttributes];
            $class = $parentClass;
        }
        $enabled = 0 === count($attributes); // Enabled by default if there is no attributes
        $attributes = array_reverse($attributes);
        foreach ($attributes as $attribute) {
            /** @var EnabledForEnvs $attr */
            $attr = $attribute->newInstance();
            $enabled = in_array($this->env, $attr->envs());
        }

        return $enabled;
    }
}
