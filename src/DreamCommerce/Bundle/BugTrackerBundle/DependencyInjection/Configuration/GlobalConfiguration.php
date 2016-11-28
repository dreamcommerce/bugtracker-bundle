<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class GlobalConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dream_commerce_bug_tracker');

        $supportedLevels = BugHandler::getSupportedLogLevels();
        $supportedTypes = BugHandler::getSupportedCollectorTypes();

        $baseConfiguration = new BaseConfiguration();
        $baseNode = new ArrayNodeDefinition('base');
        $baseConfiguration->injectPartialNode($baseNode);

        $jiraConfiguration = new JiraConfiguration();
        $jiraNode = new ArrayNodeDefinition('jira');
        $jiraConfiguration->injectPartialNode($jiraNode);

        $psr3Configuration = new Psr3Configuration();
        $psr3Node = new ArrayNodeDefinition('psr3');
        $psr3Configuration->injectPartialNode($psr3Node);

        $doctrineConfiguration = new DoctrineConfiguration();
        $doctrineNode = new ArrayNodeDefinition('doctrine');
        $doctrineConfiguration->injectPartialNode($doctrineNode);

        $rootNode
            ->fixXmlConfig('collector')
            ->children()
                ->arrayNode('configuration')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->append($baseNode)
                        ->append($psr3Node)
                        ->append($jiraNode)
                        ->append($doctrineNode)
                    ->end()
                ->end()
                ->arrayNode('collectors')
                    ->useAttributeAsKey('name')
                    ->requiresAtLeastOneElement()
                    ->fixXmlConfig('option')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->cannotBeEmpty()->end()
                            ->integerNode('priority')
                                ->defaultValue(QueueCollectorInterface::PRIORITY_NORMAL)
                            ->end()
                            ->scalarNode('type')
                                ->defaultValue(BugHandler::COLLECTOR_TYPE_BASE)
                                ->validate()
                                    ->ifNotInArray($supportedTypes)
                                    ->thenInvalid('The type %s is not supported. Please choose one of '.json_encode($supportedTypes))
                                ->end()
                            ->end()
                            ->scalarNode('level')
                                ->defaultValue(LogLevel::WARNING)
                                ->validate()
                                    ->ifNotInArray($supportedLevels)
                                    ->thenInvalid('The level %s is not supported. Please choose one of '.json_encode($supportedLevels))
                                ->end()
                            ->end()
                            ->arrayNode('options')
                                ->useAttributeAsKey('key')
                                ->prototype('variable')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
