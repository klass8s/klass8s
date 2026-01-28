<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener;

use JetBrains\PhpStorm\ArrayShape;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Util\SignatureChecker;
use Proximity\ProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use function Symfony\Component\String\u;

final readonly class EnvSuffixedMethodsSubscriber implements EventSubscriberInterface
{
    private const string PATTERN_WORD_SEPARATORS = '/[\s\-_]+/';

    private string $envSuffix;

    private const array SUPPORTED_EVENT_NAMES = [
        ManifestMethodCalledEvent::NAME,
        ContainerMethodCalledEvent::NAME,
    ];

    /**
     * @param array<string, string[]> $eventsMap A map of event names to the list of method names this subscriber should react to for that event.
     */
    public function __construct(
        #[ArrayShape([
            ManifestMethodCalledEvent::NAME => 'string[]',
            ContainerMethodCalledEvent::NAME => 'string[]',
        ])]
        private array $eventsMap,
        string $currentEnv,
    ) {
        $diff = array_diff(array_keys($eventsMap), self::SUPPORTED_EVENT_NAMES);
        if ($diff) {
            throw new \InvalidArgumentException(sprintf('Event names "%s" are not supported by "%s".', implode('", "', $diff), $this::class));
        }

        $this->envSuffix = u($currentEnv)->title()->replaceMatches(self::PATTERN_WORD_SEPARATORS, '')->toString();
    }

    /**
     * @throws \ReflectionException
     */
    public function onMethodCalled(ManifestMethodCalledEvent|ContainerMethodCalledEvent $event, string $eventName): void
    {
        $methodNames = $this->eventsMap[$eventName] ?? [];
        if (!array_key_exists($event->methodName(), $methodNames)) {
            return;
        }

        $class = new \ReflectionObject($event->proxyable());
        if ($class->implementsInterface(ProxyInterface::class)) {
            $class = $class->getParentClass();
        }
        $methodName = $event->methodName();
        $envAwareMethodName = u($methodName)->append($this->envSuffix)->toString();
        if (!$class->hasMethod($envAwareMethodName)) {
            return;
        }

        $originalMethod = $class->getMethod($methodName);
        $envAwareMethod = $class->getMethod($envAwareMethodName);
        if (!SignatureChecker::check($originalMethod, $envAwareMethod)) {
            throw new \LogicException(sprintf('Class "%s" has env-aware version of method "%s": method "%s", but signatures do not match.', $class->name, $methodName, $envAwareMethodName));
        }

        $returnType = $envAwareMethod->getReturnType();
        $returned = $envAwareMethod->invoke($event->proxyable(), ...$event->methodParams());
        if ($envAwareMethod->hasReturnType()
            && 'void' !== $returnType->getName()
            && (null !== $returned || $returnType->allowsNull())
        ) {
            $event->setReturnValue($returned);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return array_fill_keys(self::SUPPORTED_EVENT_NAMES, ['onMethodCalled', -1024]);
    }
}
