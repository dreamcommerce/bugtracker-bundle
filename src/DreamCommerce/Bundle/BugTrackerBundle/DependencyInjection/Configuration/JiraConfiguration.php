<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

use Psr\Log\LogLevel;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class JiraConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('jira');

        $baseConfiguration = new BaseConfiguration();
        $baseConfiguration->injectPartialNode($rootNode);

        $this->injectPartialNode($rootNode);

        return $treeBuilder;
    }

    public function injectPartialNode(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('label')
            ->fixXmlConfig('in_progress_status')
            ->fixXmlConfig('type')
            ->fixXmlConfig('priority')
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('connector')->defaultValue('dream_commerce_bug_tracker.jira_connector')->cannotBeEmpty()->end()
                ->scalarNode('entry_point')->cannotBeEmpty()->end()
                ->scalarNode('username')->cannotBeEmpty()->end()
                ->scalarNode('password')->cannotBeEmpty()->end()
                ->scalarNode('project')->cannotBeEmpty()->end()
                ->booleanNode('use_counter')->defaultTrue()->end()
                ->integerNode('counter_field_id')->defaultValue(10300)->end()
                ->integerNode('counter_max_value')->defaultValue(1000)->end()
                ->booleanNode('use_token')->defaultTrue()->end()
                ->integerNode('token_field_id')->defaultValue(12400)->end()
                ->scalarNode('token_field_name')->defaultValue('hash')->cannotBeEmpty()->end()
                ->scalarNode('assignee')->cannotBeEmpty()->end()
                ->arrayNode('in_progress_statuses')
                    ->treatNullLike(array())
                    ->prototype('integer')->end()
                    ->defaultValue(array(11, 21, 31, 51))
                ->end()
                ->integerNode('reopen_status')->defaultValue(51)->end()
                ->booleanNode('use_reopen')->defaultTrue()->end()
                ->integerNode('default_type')->defaultValue(1)->end()
                ->arrayNode('types')
                    ->treatNullLike(array(
                        LogLevel::EMERGENCY => 3, // emergency
                    ))
                    ->useAttributeAsKey('name')
                    ->prototype('integer')->end()
                ->end()
                ->integerNode('default_priority')->defaultValue(2)->end()
                ->arrayNode('priorities')
                    ->treatNullLike(array(
                        LogLevel::WARNING => 1, // minor
                        LogLevel::ERROR => 2, // normal
                        LogLevel::ALERT => 3, // major
                        LogLevel::CRITICAL => 4, // critical
                        LogLevel::EMERGENCY => 5, // blocker
                    ))
                    ->useAttributeAsKey('name')
                    ->prototype('integer')->end()
                ->end()
                ->arrayNode('labels')
                    ->treatNullLike(array())
                    ->prototype('scalar')->end()
                    ->defaultValue(array('app'))
                ->end()
                ->arrayNode('fields')->end()
            ->end();
    }
}
