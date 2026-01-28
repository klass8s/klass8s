<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection;

use Klass8s\Bridge\Helm\Chart\Reference\ChartReference;
use Klass8s\Bundle\DependencyInjection\Compiler\DefaultMetadataLabelsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\DefaultSelectorLabelsPass;
use Klass8s\Bundle\DependencyInjection\Compiler\DefaultServiceSelectorPass;
use Klass8s\Bundle\DependencyInjection\ContainerResources\ResourcesReferenceResolver;
use Klass8s\Event\ContainerMethodCalledEvent;
use Klass8s\Event\ManifestMethodCalledEvent;
use Klass8s\Event\ManifestMethodEvent;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Klass8sExtension extends Extension
{
    private readonly ResourcesReferenceResolver $referenceResolver;

    public function __construct()
    {
        $this->referenceResolver = new ResourcesReferenceResolver();
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../../config')
        );
        $loader->load('services.yaml');

        $appsConfigs = array_column($configs, 'apps');
        $normalizedAppsConfigs = [];
        foreach ($appsConfigs as $i => $appsConfig) {
            foreach ($appsConfig as $appConfig) {
                $normalizedAppsConfigs[$i][$appConfig['alias']] = $appConfig;
            }
        }
        if (2 > count($normalizedAppsConfigs)) {
            $normalizedAppsConfigs[] = []; // array_replace_recursive will have enough arguments
        }

        // We use ArrayNode::ignoreExtraKeys() for app configs in Configuration class;
        // therefore, Symfony will not merge app configs properly - thus we need to do this ourselves
        $finalAppsConfig = array_replace_recursive(...$normalizedAppsConfigs);

        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $config['apps'] = array_replace_recursive($config['apps'], $finalAppsConfig);

        $projectDir = $container->getParameter('kernel.project_dir');
        foreach ($config['helm']['releases'] ?? [] as $releaseConfig) {
            $ref = new ChartReference($releaseConfig['chart']);
            if ($ref->isRelativePath()) {
                $path = $projectDir.'/'.ltrim($ref->value, './\\');
                self::ensureChartDirExists($path);
            } elseif ($ref->isAbsolutePath()) {
                self::ensureChartDirExists($ref->value);
            }
        }

        $this->loadInternal($config, $container);
    }

    public function getAlias(): string
    {
        return 'klass8s';
    }

    /**
     * @throws \Exception
     */
    private function loadInternal(array $config, ContainerBuilder $container): void
    {
        $this
            ->setupCodeDir($config, $container)
            ->setupManifestsDir($config, $container)
            ->setupTemplatesDir($container)
            ->setupNamespacePrefix($config['namespace_prefix'], $container)
        ;

        $container->setParameter('klass8s.auto_set_resources', $config['auto_set_resources']);
        $container->setParameter('klass8s.auto_set_replicas', $config['auto_set_replicas']);
        $container->setParameter('klass8s.auto_generate_checksums', $config['auto_generate_checksums']);

        $container->setParameter(DefaultMetadataLabelsPass::PARAM, $config[Configuration::PARAM_SET_DEFAULT_METADATA_LABELS]);
        $container->setParameter(DefaultSelectorLabelsPass::PARAM, $config[Configuration::PARAM_SET_DEFAULT_SELECTOR_LABELS]);
        $container->setParameter(DefaultServiceSelectorPass::PARAM, $config[Configuration::PARAM_SET_DEFAULT_SERVICE_SELECTOR]);

        foreach ($config['apps'] as $appAlias => $appConfig) {
            foreach ($appConfig['manifests'] as $manifestName => $manifestConfig) {
                $resources = $manifestConfig['resources'] ?? [];

                foreach (['requests', 'limits'] as $resourcesKey) {
                    $resourcesSection = $resources[$resourcesKey] ?? [];
                    if (is_string($resourcesSection)) {
                        $config['apps'][$appAlias]['manifests'][$manifestName]['resources'][$resourcesKey] = $this->referenceResolver->resolve($resourcesSection, $resourcesKey, $appAlias, $config['apps']);
                    }
                }
            }
        }

        $eventMap = [];
        foreach ($config['env_management']['env_suffixed_methods'] ?? [] as $envSuffixedMethodConfig) {
            if (!is_array($envSuffixedMethodConfig)) {
                throw new \LogicException('$envSuffixedMethodConfig must be an array.');
            }

            $methodName = $envSuffixedMethodConfig['name'];

            $conditions = [];
            foreach (['if', 'if_not'] as $conditionName) {
                $condition = $envSuffixedMethodConfig[$conditionName] ?? null;
                if (null !== $condition) {
                    if (is_string($condition)) {
                        $condition = $container->getParameterBag()->resolveValue($condition);
                        if (!is_bool($condition)) {
                            throw $this->exceptionForEnvAwareMethod('if', $methodName, $condition);
                        }
                    } elseif (!is_bool($condition)) {
                        throw $this->exceptionForEnvAwareMethod('if', $methodName, $condition);
                    }

                    $conditions[$conditionName] = $condition;
                }
            }

            if (count($conditions) > 1) {
                throw new InvalidConfigurationException(sprintf('Both "if" and "if_not" keys are set for env-suffixed method "%s".', $methodName));
            } elseif (null !== $conditions['if'] ?? null) {
                $methodEnabled = $conditions['if'];
            } elseif (null !== $conditions['if_not'] ?? null) {
                $methodEnabled = !$conditions['if_not'];
            } else {
                $methodEnabled = true;
            }

            if (!$methodEnabled) {
                continue;
            }

            foreach ($envSuffixedMethodConfig['for'] as $i => $proxyableType) {
                $eventName = match ($proxyableType) {
                    'manifest' => ManifestMethodEvent::NAME,
                    'container' => ContainerMethodCalledEvent::NAME,
                    default => throw new InvalidConfigurationException(sprintf('Invalid value "%s" under configuration key `for.%d` for env-suffixed method "%s". Supported values are "manifest" and "container".', $proxyableType, $i, $methodName)),
                };
                $eventMap[$eventName][] = $methodName;
            }

            $eventMap = array_map('array_unique', $eventMap);
        }

        $container->setParameter('klass8s.env_management.env_suffixed_methods.event_map', $eventMap);
        $container->setParameter('klass8s.config.apps', $config['apps']);
        $container->setParameter('klass8s.config.helm', $config['helm'] ?? []);
        $container->setParameter('klass8s.config.helm.repositories', $config['helm']['repositories'] ?? []);
    }

    private function setupTemplatesDir(ContainerBuilder $container): static
    {
        $container->setParameter(
            'klass8s.class_templates_dir',
            __DIR__.'/../../../templates',
        );

        return $this;
    }

    private function setupNamespacePrefix(string $prefix, ContainerBuilder $container): void
    {
        $container->setParameter('klass8s.namespace_prefix', $prefix);
    }

    private function setupCodeDir(array $config, ContainerBuilder $container): static
    {
        $codeDir = $config['code_dir'] ?? null;
        if (null === $codeDir) {
            $srcDir = $this->getSrcDir($container);
            if (!file_exists($srcDir)) {
                throw $this->createExceptionForSrcDir($srcDir);
            }
            $codeDir = $srcDir.DIRECTORY_SEPARATOR.'K8S';
        }
        if (!file_exists($codeDir)) {
            try {
                @mkdir($codeDir, 0o700, true);
            } catch (\Throwable) {
            }
        }
        $container->setParameter('klass8s.code_dir', $codeDir);

        return $this;
    }

    private function setupManifestsDir(array $config, ContainerBuilder $container): static
    {
        $configParam = 'manifests_dir';
        $manifestsDir = $config[$configParam] ?? null;
        if (null === $manifestsDir) {
            $varDir = $container->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.'var';
            if (!file_exists($varDir)) {
                throw new \InvalidArgumentException(sprintf('Parameter "%s" is not set, default value is "var" directory, which was resolved to "%s", but no such directory exists.', $configParam, $varDir));
            }
            $manifestsDir = $varDir.DIRECTORY_SEPARATOR.'k8s-manifests';
        }
        if (!file_exists($manifestsDir)) {
            @mkdir($manifestsDir, 0o777, true);
        }
        $container->setParameter('klass8s.manifests_dir', $manifestsDir);

        return $this;
    }

    private function getSrcDir(ContainerBuilder $container): string
    {
        return $container->getParameter('kernel.project_dir').DIRECTORY_SEPARATOR.'src';
    }

    private function createExceptionForSrcDir(string $srcDir): InvalidConfigurationException
    {
        $errMessage = <<<ERR
            "klass8s.code_dir" config value was not specified.
            Fallback value requires %kernel.project_dir%/src dir, which was resolved
            to "$srcDir", but this directory does not exist. Please configure "klass8s.code_dir"
            config value explicitly as an absolute path or use standard "src" directory
            for your code.
            ERR;
        $errMessage = str_replace(PHP_EOL, ' ', $errMessage);

        return new InvalidConfigurationException($errMessage);
    }

    private static function ensureChartDirExists(string $path): void
    {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('Chart directory "%s" does not exist.', $path));
        }
        if (!is_dir($path)) {
            throw new \InvalidArgumentException(sprintf('Chart directory "%s" is a file.', $path));
        }
    }

    private static function exceptionForEnvAwareMethod(string $key, string $methodName, mixed $when): InvalidConfigurationException
    {
        return new InvalidConfigurationException(sprintf('Key "%s" in a config for env-suffixed method "%s" must be a boolean or a reference to a boolean parameter, got "%s".', $methodName, is_string($when) ? $when : get_debug_type($when)));
    }
}
