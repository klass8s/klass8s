<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Runtime\StatusResolver\Strategy;

use Klass8s\Bundle\EnvManagement\Attribute\EnabledForConfigurationParameter;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Runtime\ManifestStatusResolverStrategyInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class EnabledForConfigurationParameterStrategy implements ManifestStatusResolverStrategyInterface
{
    public function __construct(private ParameterBagInterface $parameters)
    {
    }

    public function isClassEnabled(\ReflectionClass $class): bool
    {
        /** @var EnabledForConfigurationParameter|null $attribute */
        $attribute = AttributesUtil::fromClassOrParents($class, EnabledForConfigurationParameter::class);
        if (null === $attribute) {
            return true;
        }

        if (!$this->parameters->has($attribute->parameter)) {
            throw new \RuntimeException(
                sprintf(
                    'Parameter "%s", used in PHP attribute "%s", does not exist in Symfony service container',
                    $attribute->parameter,
                    EnabledForConfigurationParameter::class
                )
            );
        }
        $parameter = $this->parameters->resolveValue($this->parameters->get($attribute->parameter));
        if (!is_bool($parameter)) {
            throw new \RuntimeException(
                sprintf(
                    'Parameter "%s", used in PHP attribute "%s", must contain a bool value, %s is given',
                    $attribute->parameter,
                    EnabledForConfigurationParameter::class,
                    gettype($parameter)
                )
            );
        }

        return $parameter;
    }
}
