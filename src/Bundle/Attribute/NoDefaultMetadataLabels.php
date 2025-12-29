<?php

declare(strict_types=1);

namespace Klass8s\Bundle\Attribute;

/**
 * This attribute is used on workload manifests like Deployments and Jobs
 * to specify that default metadata labels should not be applied to them.
 * Note that if default selector labels are set on a workload manifest, that still
 * will be copied to its `metadata.labels` and pod template's `metadata.labels` by
 * resource makers, like DeploymentMaker.
 * @see \Klass8s\Bundle\EventListener\DefaultMetadataLabelsSubscriber
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class NoDefaultMetadataLabels
{
}
