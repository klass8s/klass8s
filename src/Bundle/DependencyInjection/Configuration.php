<?php

declare(strict_types=1);

namespace Klass8s\Bundle\DependencyInjection;

use Klass8s\App\AppInterface;
use Klass8s\Bridge\Helm\Chart\Reference\ChartReference;
use Klass8s\Bundle\DependencyInjection\ContainerResources\ContainerResourcesConfiguration;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

readonly class Configuration implements ConfigurationInterface
{
    public const string PARAM_SET_DEFAULT_SELECTOR_LABELS = 'set_default_selector_labels';
    public const string PARAM_SET_DEFAULT_METADATA_LABELS = 'set_default_metadata_labels';
    public const string PARAM_SET_DEFAULT_SERVICE_SELECTOR = 'set_default_service_selector';

    private ContainerResourcesConfiguration $resourcesConfiguration;
    private Processor $processor;

    public function __construct()
    {
        $this->resourcesConfiguration = new ContainerResourcesConfiguration();
        $this->processor = new Processor();
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('klass8s');
        $root = $treeBuilder->getRootNode();

        $root
            ->children()
                ->append($this->appsNode())
                ->append($this->helmNode())
                ->arrayNode('env_management')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('env_suffixed_methods')
                            ->cannotBeOverwritten()
                            ->defaultValue([
                                ['name' => 'replicas', 'for' => ['container', 'manifest']],
                                ['name' => 'resources', 'for' => ['container', 'manifest']],
                            ])
                            ->beforeNormalization()
                                ->ifArray()
                                ->then(static fn (array $v) => array_map(static fn ($item) => is_string($item) ? ['name' => $item] : $item, $v))
                            ->end()
                            ->arrayPrototype()
                                ->addDefaultsIfNotSet()
                                ->beforeNormalization()
                                    ->ifString()
                                    ->then(static fn (string $name): array => ['name' => $name])
                                ->end()
                                ->children()
                                    ->stringNode('name')
                                        ->cannotBeEmpty()
                                        ->isRequired()
                                    ->end()
                                    ->arrayNode('for')
                                        ->cannotBeEmpty()
                                        ->defaultValue(['container', 'manifest'])
                                        ->enumPrototype()
                                            ->values(['container', 'manifest'])
                                        ->end()
                                    ->end()
                                    ->booleanNode('if')
                                        ->defaultNull()
                                        ->info('Boolean value or a boolean parameter reference, that, when resolved, will determine if the env-suffixed method should be enabled.')
                                    ->end()
                                    ->booleanNode('if_not')
                                        ->defaultNull()
                                        ->info('Boolean value or a boolean parameter reference, that, when resolved, will determine if the env-suffixed method should be enabled (by negation).')
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->stringNode('code_dir')->defaultNull()->end()
                ->stringNode('namespace_prefix')->defaultValue('App\\K8S\\')->end()
                ->stringNode('manifests_dir')->defaultNull()->end()
                ->booleanNode('auto_set_resources')
                    ->defaultFalse()
                ->end()
                ->booleanNode('auto_set_replicas')
                    ->defaultFalse()
                ->end()
                ->booleanNode('auto_generate_checksums')
                    ->defaultTrue()
                ->end()
                ->booleanNode(self::PARAM_SET_DEFAULT_SELECTOR_LABELS)
                    ->defaultTrue()
                ->end()
                ->booleanNode(self::PARAM_SET_DEFAULT_METADATA_LABELS)
                    ->defaultTrue()
                ->end()
                ->booleanNode(self::PARAM_SET_DEFAULT_SERVICE_SELECTOR)
                    ->defaultTrue()
                ->end()
                ->booleanNode('strip_namespace')
                    ->defaultFalse()
                    ->info('When true, strips the "namespace" field from metadata of all generated manifests (both Klass8s apps and Helm releases).')
                ->end()
            ->end();

        return $treeBuilder;
    }

    public function appsNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('apps');
        $node = $treeBuilder->getRootNode();

        $node
            ->useAttributeAsKey('alias')
            ->arrayPrototype()
                ->ignoreExtraKeys(false)
                ->canBeDisabled()
                ->children()
                    ->stringNode('class')
                        ->cannotBeEmpty()
                        ->cannotBeOverwritten()
                        ->beforeNormalization()
                            ->ifString()
                            ->then(static::validAppClassName(...))
                        ->end()
                    ->end()
                    ->integerNode('deploy_priority')
                        ->defaultValue(0)
                    ->end()
                    ->arrayNode('manifests')
                        ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->ignoreExtraKeys(false)
                            ->children()
                                ->booleanNode('virtual')
                                    ->defaultFalse()
                                ->end()
                                ->integerNode('replicas')
                                ->end()
                                ->arrayNode('resources')
                                    ->children()
                                        ->variableNode('requests')
                                            ->validate()
                                                ->ifArray()
                                                ->then($this->validResourcesNodeValue(...))
                                            ->end()
                                        ->end()
                                        ->variableNode('limits')
                                            ->validate()
                                                ->ifArray()
                                                ->then($this->validResourcesNodeValue(...))
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private function helmNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('helm');
        $node = $treeBuilder->getRootNode();

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('repositories')
                    ->useAttributeAsKey('name')
                    ->defaultValue([])
                    ->stringPrototype()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
                ->arrayNode('releases')
                    ->useAttributeAsKey('name')
                    ->defaultValue([])
                    ->arrayPrototype()
                        ->children()
                            ->stringNode('chart')
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->validate()
                                    ->ifString()
                                    ->then(static::validChartReference(...))
                                ->end()
                            ->end()
                            ->stringNode('version')
                                ->defaultNull()
                            ->end()
                            ->variableNode('values')
                                ->defaultNull()
                            ->end()
                            ->stringNode('values_from')
                                ->defaultNull()
                            ->end()
                            ->integerNode('deploy_priority')
                                ->defaultValue(0)
                            ->end()
                        ->end()
                        ->validate()
                            ->ifTrue(fn (array $v): bool => isset($v['values'], $v['values_from']))
                            ->thenInvalid('Cannot use both "values" and "values_from" in the same release configuration.')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }

    private static function validAppClassName(string $class): string
    {
        if (!class_exists($class)) {
            throw new InvalidConfigurationException(sprintf('Class "%s" does not exist', $class));
        }

        $reflection = new \ReflectionClass($class);
        if (!$reflection->implementsInterface(AppInterface::class)) {
            throw new InvalidConfigurationException(sprintf('App  class "%s" must implement AppInterface', $class));
        }

        return $class;
    }

    private static function validChartReference(string $value): string
    {
        try {
            $reference = new ChartReference($value);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidConfigurationException($e->getMessage());
        }

        return $reference->value;
    }

    private function validResourcesNodeValue(array $nodeValue): array
    {
        return $this->processor->processConfiguration($this->resourcesConfiguration, [$nodeValue]);
    }
}
