<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection\Compiler;

use Klass8s\Bundle\DependencyInjection\Compiler\Traits\GetAppAliasTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final readonly class SetAppConfigPass implements CompilerPassInterface
{
    use GetAppAliasTrait;

    public function process(ContainerBuilder $container): void
    {
        $appConfigs = $container->getParameter('klass8s.config.apps');

        foreach ($container->findTaggedServiceIds(AppsPass::APP_TAG) as $id => $tags) {
            $appDefinition = $container->getDefinition($id);
            if ($appDefinition->getClass() === $id) {
                // Skip autowired app services, they will be removed in RemoveAutowiredAppsPass
                continue;
            }
            $alias = $this->getAppAlias($id, $tags);
            $appConfig = $appConfigs[$alias] ?? [];
            $appDefinition->addMethodCall('setConfig', [$appConfig]);
        }
    }
}
