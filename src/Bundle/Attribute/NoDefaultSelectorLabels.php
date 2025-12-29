<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Attribute;

/**
 * This attribute is used on workload manifests like Deployments and Jobs
 * to specify, that default selector labels should not be applied to them.
 * @see \Klass8s\Bundle\EventListener\DefaultSelectorLabelsSubscriber
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NoDefaultSelectorLabels
{
}
