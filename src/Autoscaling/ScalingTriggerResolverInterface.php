<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;

/**
 * Turns one pure-data scaling trigger attribute into concrete KEDA triggers. Resolvers are
 * regular DI services (tagged automatically via autoconfiguration), so unlike attributes they
 * may depend on anything the container knows — endpoints, naming conventions, parameters.
 *
 * One attribute may fan out into several triggers (e.g. a derived trigger per transport the
 * workload consumes) — return a list in that case; an empty list is invalid.
 */
interface ScalingTriggerResolverInterface
{
    public function supports(ScalingTriggerAttributeInterface $attribute): bool;

    /**
     * @return Trigger|list<Trigger>
     */
    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): Trigger|array;
}
