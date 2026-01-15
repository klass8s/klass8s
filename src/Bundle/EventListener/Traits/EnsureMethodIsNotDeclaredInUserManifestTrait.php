<?php

declare(strict_types=1);

namespace Klass8s\Bundle\EventListener\Traits;

use Klass8s\Core\Deployment\DeploymentInterface;
use Klass8s\Core\StatefulSet\StatefulSetInterface;
use Proximity\ProxyInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait EnsureMethodIsNotDeclaredInUserManifestTrait
{
    private string $env;

    protected function ensureMethodIsNotDeclaredInUserManifest(
        DeploymentInterface|StatefulSetInterface $manifest,
        string $methodName,
    ): void {
        $class = new \ReflectionObject($manifest);
        if ($class->implementsInterface(ProxyInterface::class)) {
            $class = $class->getParentClass();
        }

        $basicMethod = $class->getMethod($methodName);
        $envSpecificMethodName = $methodName.ucfirst($this->env);
        $envSpecificMethod = $class->hasMethod($envSpecificMethodName)
            ? $class->getMethod($envSpecificMethodName)
            : null;

        foreach ([$basicMethod, $envSpecificMethod] as $method) {
            /** @var \ReflectionMethod $method */
            if ($method?->getDeclaringClass() !== $class) {
                continue;
            }

            throw new \LogicException(
                sprintf(
                    '%s for manifest "%s" in app "%s" are declared both in manifests\' config and in method %s::%s(), '
                    .'which leads to ambiguity. Please choose one way to define %s for this manifest.',
                    ucfirst($methodName),
                    $class->name,
                    $manifest->app->alias,
                    $class->getShortName(),
                    $method->getName(),
                    $methodName,
                )
            );
        }
    }

    #[Required]
    public function setCurrentEnv(string $env): void
    {
        $this->env = $env;
    }
}
