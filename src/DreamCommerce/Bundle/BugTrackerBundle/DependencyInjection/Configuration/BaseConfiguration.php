<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class BaseConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('base');

        $this->injectPartialNode($rootNode);

        return $treeBuilder;
    }

    public function injectPartialNode(ArrayNodeDefinition $node)
    {
        $node
            ->fixXmlConfig('exception')
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
            ->end();
    }
}
