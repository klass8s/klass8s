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

    /**
     * @return list<Trigger>
     */
    public function resolve(ScalingTriggerAttributeInterface $attribute, AutoscaleContext $context): array
    {
        foreach ($this->resolvers as $resolver) {
            if (!$resolver->supports($attribute)) {
                continue;
            }

            $resolved = $resolver->resolve($attribute, $context);
            $triggers = $resolved instanceof Trigger ? [$resolved] : array_values($resolved);
            if ([] === $triggers) {
                throw new \LogicException(sprintf('Resolver "%s" produced no triggers for attribute "%s" — a ScaledObject trigger cannot be empty.', $resolver::class, $attribute::class));
            }
            foreach ($triggers as $trigger) {
                if (!$trigger instanceof Trigger) {
                    throw new \LogicException(sprintf('Resolver "%s" must return %s instances, %s given.', $resolver::class, Trigger::class, get_debug_type($trigger)));
                }
            }

            return $triggers;
        }

        throw new \LogicException(sprintf('No scaling trigger resolver supports the attribute "%s". Register a service implementing "%s" that supports it.', $attribute::class, ScalingTriggerResolverInterface::class));
    }
}
