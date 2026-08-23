<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;

/**
 * Turns one pure-data scaling trigger attribute into a concrete KEDA trigger. Resolvers are
 * regular DI services (tagged automatically via autoconfiguration), so unlike attributes they
 * may depend on anything the container knows — endpoints, naming conventions, parameters.
 */
interface ScalingTriggerResolverInterface
{
    public function supports(ScalingTriggerAttributeInterface $attribute): bool;

    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): Trigger;
}
