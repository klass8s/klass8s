<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\App\AppInterface;
use Klass8s\Bundle\DependencyInjection\Compiler\Traits\GetAppAliasTrait;
use Klass8s\Bundle\EnvManagement\Attribute\EnabledForApps;
use Klass8s\Bundle\Klass8sBundle;
use Klass8s\Bundle\Util\AttributesUtil;
use Klass8s\Core\Container\ContainerInterface;
use Klass8s\Manifest\ManifestInterface;
use Klass8s\Registry\ManifestRegistry;
use Klass8s\Util\StringUtil;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\TypedReference;

final class ManifestsPass implements CompilerPassInterface
{
    use GetAppAliasTrait;

    private array $appReflectionsCache = [];

    /**
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        $registryDefinition = $container->getDefinition(ManifestRegistry::class);

        $classNameToAliasesMap = $this->createAppClassToAliasesMap($container);
        foreach ($container->findTaggedServiceIds(Klass8sBundle::TAG_MANIFEST) as $id => $tags) {
            if (!class_exists($id)) {
                continue;
            }

            $manifestDefinition = $container->getDefinition($id);
            $manifestClass = new \ReflectionClass($manifestDefinition->getClass());
            if (!$manifestClass->implementsInterface(ManifestInterface::class)) {
                throw new \LogicException(sprintf('Only %s instances must be tagged with tag "%s"', ManifestInterface::class, Klass8sBundle::TAG_MANIFEST));
            }

            $manifestName = $manifestClass->getMethod('manifestName')->invoke(null);
            $manifestKind = $manifestClass->getMethod('kind')->invoke(null);

            foreach ($classNameToAliasesMap as $className => $aliases) {
                $appClass = $this->getAppReflection($className);
                if (!str_starts_with($manifestDefinition->getClass(), $appClass->getNamespaceName().'\\')) {
                    // We need to find an app class which "owns" this manifest
                    continue;
                }

                foreach ($aliases as $alias) {
                    if (!$this->manifestEnabledForApp($manifestClass, $alias)) {
                        continue;
                    }

                    $dedicatedManifestDefinition = clone $manifestDefinition;
                    $newId = sprintf(
                        'klass8s.apps.%s.manifests.%s_%s',
                        StringUtil::underscored($alias),
                        StringUtil::underscored($manifestName),
                        StringUtil::underscored($manifestKind)
                    );

                    if ($container->hasDefinition($newId)) {
                        throw new \LogicException(sprintf('Classes %s and %s have the same kind and same shortName, but this combination must be unique within the app boundaries', $dedicatedManifestDefinition->getClass(), $container->getDefinition($newId)->getClass()));
                    }

                    $container->setDefinition($newId, $dedicatedManifestDefinition);

                    $appRef = new Reference(AppsPass::appDefinitionId($alias));
                    $dedicatedManifestDefinition->addMethodCall('setApp', [$appRef]);

                    $registryDefinition->addMethodCall('add', [new Reference($newId)]);

                    $this->autowireContainerClasses($container, $newId, $alias);
                }
            }
        }
    }

    private function autowireContainerClasses(ContainerBuilder $container, string $manifestDefinitionId, string $appAlias): void
    {
        $manifestDefinition = $container->getDefinition($manifestDefinitionId);
        foreach ($manifestDefinition->getArguments() as $name => $argument) {
            if (!$argument instanceof TypedReference) {
                continue;
            }
            $type = $argument->getType();
            if (!class_exists($type)) {
                continue;
            }
            $class = new \ReflectionClass($type);
            if (!$class->implementsInterface(ContainerInterface::class)) {
                continue;
            }
            $containerDefinition = $container->getDefinition($class->getName());
            $newDefinition = clone $containerDefinition;
            $id = $manifestDefinitionId.'.containers.'.$name;
            $container->setDefinition($id, $newDefinition);
            $manifestDefinition->replaceArgument($name, new Reference($id));
            $this->autowireContainerClass($container, $id, $appAlias);
        }
    }

    private function autowireContainerClass(ContainerBuilder $container, string $containerDefinitionId, string $appAlias): void
    {
        $containerDefinition = $container->getDefinition($containerDefinitionId);
        $appDefinitionId = AppsPass::appDefinitionId($appAlias);
        $appDefinition = $container->getDefinition($appDefinitionId);
        foreach ($containerDefinition->getArguments() as $name => $argument) {
            if (!$argument instanceof TypedReference) {
                continue;
            }
            $type = $argument->getType();
            if (!class_exists($type)) {
                continue;
            }
            $class = new \ReflectionClass($type);
            if (!$class->implementsInterface(AppInterface::class)) {
                continue;
            }
            if ($class->getName() !== $appDefinition->getClass()) {
                continue;
            }
            $containerDefinition->replaceArgument($name, new Reference($appDefinitionId));
        }
    }

    private function createAppClassToAliasesMap(ContainerBuilder $container): array
    {
        $map = [];
        foreach ($container->findTaggedServiceIds(Klass8sBundle::TAG_APP) as $id => $tags) {
            $definition = $container->getDefinition($id);
            $className = $definition->getClass();

            // Default (autowired) app services will be removed later in RemoveAutowiredAppsPass
            if ($id === $className) {
                continue;
            }

            $alias = $this->getAppAlias($id, $tags);

            $map[$className][] = $alias;
        }

        return $map;
    }

    /**
     * @throws \ReflectionException
     */
    private function getAppReflection(string $className): \ReflectionClass
    {
        if (!array_key_exists($className, $this->appReflectionsCache)) {
            $this->appReflectionsCache[$className] = new \ReflectionClass($className);
        }

        return $this->appReflectionsCache[$className];
    }

    private function manifestEnabledForApp(\ReflectionClass $manifestClass, string $appAlias): bool
    {
        /** @var EnabledForApps $attribute */
        $attribute = AttributesUtil::fromClassOrParents($manifestClass, EnabledForApps::class);
        if (null === $attribute) {
            return true;
        }

        return in_array($appAlias, $attribute->appAliases());
    }
}
