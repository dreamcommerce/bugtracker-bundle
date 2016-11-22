<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection;

use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dream_commerce_bug_tracker');

        $supportedPriorities = array(
            QueueCollectorInterface::PRIORITY_LOW,
            QueueCollectorInterface::PRIORITY_NORMAL,
            QueueCollectorInterface::PRIORITY_HIGH,
        );

        $supportedLevels = array(
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY,
        );

        $rootNode
            ->fixXmlConfig('handler')
            ->children()
                ->arrayNode('configuration')
                    ->children()
                        ->arrayNode('default')
                            ->fixXmlConfig('ignore_exception')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->arrayNode('exceptions')
                                    ->treatNullLike(array())
                                    ->prototype('scalar')->end()
                                ->end()
                                ->arrayNode('ignore_exceptions')
                                    ->treatNullLike(array())
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('jira')
                            ->fixXmlConfig('label')
                            ->fixXmlConfig('in_progress_status')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('entry_point')->cannotBeEmpty()->end()
                                ->scalarNode('username')->cannotBeEmpty()->end()
                                ->scalarNode('password')->cannotBeEmpty()->end()
                                ->scalarNode('project')->cannotBeEmpty()->end()
                                ->scalarNode('counter_field_id')->defaultValue('10300')->cannotBeEmpty()->end()
                                ->scalarNode('hash_field_id')->defaultValue('12400')->cannotBeEmpty()->end()
                                ->scalarNode('assignee')->cannotBeEmpty()->end()
                                ->arrayNode('in_progress_statuses')
                                    ->treatNullLike(array())
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('11', '21', '31', '51'))
                                ->end()
                                ->scalarNode('reopen_status')->defaultValue('51')->cannotBeEmpty()->end()
                                ->scalarNode('default_type')->defaultValue('1')->cannotBeEmpty()->end()
                                ->arrayNode('labels')
                                    ->treatNullLike(array())
                                    ->prototype('scalar')->end()
                                    ->defaultValue(array('app'))
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('collectors')
                    ->fixXmlConfig('option')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->cannotBeEmpty()->end()
                            ->scalarNode('priority')
                                ->defaultValue(QueueCollectorInterface::PRIORITY_NORMAL)
                                ->validate()
                                    ->ifNotInArray($supportedPriorities)
                                    ->thenInvalid('The priority %s is not supported. Please choose one of '.json_encode($supportedPriorities))
                                ->end()
                            ->end()
                            ->scalarNode('level')
                                ->defaultValue(LogLevel::WARNING)
                                ->validate()
                                    ->ifNotInArray($supportedLevels)
                                    ->thenInvalid('The level %s is not supported. Please choose one of '.json_encode($supportedLevels))
                                ->end()
                            ->end()
                            ->variableNode('options')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
