<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Attribute;

/**
 * This attribute is used on Service manifests to specify, that default selector
 * should not be applied to them. Note that default service selector along with
 * default selector labels on Deployment or StatefulSet allows you to wire together
 * Service and Deployment/StatefulSet just by specifying the same short name for them.
 * Short name is usually a result of ManifestClass::shortName() method, but for some
 * dynamically named manifests is returned by $manifestInstance->name() method.
 * @see \Klass8s\Bundle\EventListener\DefaultSelectorLabelsSubscriber
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NoDefaultServiceSelector
{
}
