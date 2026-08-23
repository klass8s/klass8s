<?php

declare(strict_types=1);

namespace Klass8s\Autoscaling;

use Klass8s\Autoscaling\Attribute\ScalingTriggerAttributeInterface;
use Klass8s\Autoscaling\Keda\API\ScaledObject\Trigger;

final class ScalingTriggerResolversRegistry
{
    /**
     * @var ScalingTriggerResolverInterface[]
     */
    private readonly array $resolvers;

    /**
     * @param ScalingTriggerResolverInterface[] $resolvers
     */
    public function __construct(iterable $resolvers)
    {
        $this->resolvers = $resolvers instanceof \Traversable ? iterator_to_array($resolvers) : $resolvers;
    }

    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): Trigger
    {
        foreach ($this->resolvers as $resolver) {
            if ($resolver->supports($attribute)) {
                return $resolver->resolve($attribute, $context);
            }
        }

        throw new \LogicException(sprintf('No scaling trigger resolver supports the attribute "%s". Register a service implementing "%s" that supports it.', $attribute::class, ScalingTriggerResolverInterface::class));
    }
}
