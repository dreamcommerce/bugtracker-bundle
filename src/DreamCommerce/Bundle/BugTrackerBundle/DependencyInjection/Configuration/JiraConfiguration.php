<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

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
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('http_client')->defaultValue('dream_commerce.http_client')->cannotBeEmpty()->end()
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
            ->end();
    }
}
